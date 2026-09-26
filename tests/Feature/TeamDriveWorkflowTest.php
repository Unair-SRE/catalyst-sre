<?php

use App\Actions\Payments\ConfirmDrivePayment;
use App\Actions\Payments\RejectCompetitionPayment;
use App\Actions\Payments\VerifyCompetitionPayment;
use App\Actions\Registrations\RegisterTeam;
use App\Actions\Teams\CreateDriveTeam;
use App\Actions\Teams\SaveDriveTeamMember;
use App\Actions\Teams\UpdateDriveFolder;
use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Livewire\Dashboard\CompetitionPaymentForm;
use App\Livewire\Dashboard\TeamManagement;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Rules\GoogleDriveFolder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function driveTeam(): Team
{
    return app(CreateDriveTeam::class)->handle(User::factory()->create(), [
        'name' => 'Drive Team', 'institution' => 'Universitas Airlangga',
        'documents_drive_url' => 'https://drive.google.com/drive/folders/team-folder',
    ]);
}

function drivePayment(): Payment
{
    $team = driveTeam();

    return app(RegisterTeam::class)->handle($team->captain, $team, Competition::factory()->code(CompetitionCode::MiniCase)->create())->payment;
}

test('Drive folder validation accepts folder sharing links and rejects other URLs', function (string $url, bool $valid) {
    expect(GoogleDriveFolder::valid($url))->toBe($valid);
})->with([
    ['https://drive.google.com/drive/folders/abc_123-XYZ?usp=sharing', true],
    ['https://drive.google.com/drive/u/0/folders/abc', true],
    ['http://drive.google.com/drive/folders/abc', false],
    ['https://drive.google.com.evil.test/drive/folders/abc', false],
    ['https://drive.google.com@evil.test/drive/folders/abc', false],
    ['https://user@drive.google.com/drive/folders/abc', false],
    ['https://drive.google.com/file/d/abc/view', false],
    ['https://drive.google.com/drive/folders/', false],
    ['javascript:alert(1)', false],
    ['https://drive.google.com:444/drive/folders/abc', false],
]);

test('a team and its members are saved with one folder and no file upload', function () {
    config(['services.imagekit.public_key' => null, 'services.imagekit.private_key' => null]);
    $captain = User::factory()->create();
    Livewire::actingAs($captain)->test(TeamManagement::class)
        ->set('teamForm.name', 'Complete Team')->set('teamForm.institution', 'Unair')
        ->set('documentsDriveUrl', 'https://drive.google.com/drive/folders/team-folder')
        ->call('addSetupMember')
        ->set('setupMembers.0.name', 'Member One')->set('setupMembers.0.email', 'member@example.test')
        ->set('setupMembers.0.whatsapp', '08123456789')
        ->call('createTeam')->assertHasNoErrors()->assertSee('Member One')
        ->assertSee('unair@sre.co.id')->assertDontSee('type="file"', false);
    $team = $captain->fresh()->captainedTeam;
    expect($team->hasDocumentsFolder())->toBeTrue()
        ->and($team->members()->count())->toBe(1)
        ->and($team->members()->first()->ktm_file_id)->toBeNull();
});

test('incomplete setup is rejected atomically', function () {
    $captain = User::factory()->create();
    Livewire::actingAs($captain)->test(TeamManagement::class)
        ->set('teamForm.name', 'Incomplete')->set('teamForm.institution', 'Unair')
        ->set('documentsDriveUrl', 'https://evil.test/folder')
        ->call('addSetupMember')->call('createTeam')->assertHasErrors(['documentsDriveUrl', 'setupMembers.0.name']);
    $this->assertDatabaseCount('teams', 0);
    $this->assertDatabaseCount('team_members', 0);
});

test('one folder covers mini case and one main competition with separate confirmations', function () {
    $team = driveTeam();
    $first = app(RegisterTeam::class)->handle($team->captain, $team, Competition::factory()->code(CompetitionCode::MiniCase)->create());
    $second = app(RegisterTeam::class)->handle($team->captain, $team, Competition::factory()->code(CompetitionCode::BusinessCase)->create());
    app(ConfirmDrivePayment::class)->handle($team->captain, $first->payment, 'Sender', true);
    expect($first->payment->fresh()->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($second->payment->fresh()->status)->toBeNull()
        ->and($team->fresh()->isLocked())->toBeTrue();
});

test('confirmation requires explicit participant acknowledgement', function () {
    $payment = drivePayment();
    app(ConfirmDrivePayment::class)->handle($payment->registration->team->captain, $payment, 'Sender', false);
})->throws(ValidationException::class);

test('confirmation cannot be submitted twice or by another captain', function () {
    $payment = drivePayment();
    $captain = $payment->registration->team->captain;
    app(ConfirmDrivePayment::class)->handle($captain, $payment, 'Sender', true);
    app(ConfirmDrivePayment::class)->handle($captain, $payment, 'Sender', true);
})->throws(ValidationException::class, 'already been submitted');

test('another captain cannot confirm payment', function () {
    $payment = drivePayment();
    app(ConfirmDrivePayment::class)->handle(User::factory()->create(), $payment, 'Sender', true);
})->throws(AuthorizationException::class);

test('admin correction notes are shown and corrected documents can be resubmitted', function () {
    $payment = drivePayment();
    $captain = $payment->registration->team->captain;
    $admin = User::factory()->admin()->create();
    app(ConfirmDrivePayment::class)->handle($captain, $payment, 'Sender', true);
    app(RejectCompetitionPayment::class)->handle($admin, $payment, 'Please grant Viewer access.');
    Livewire::actingAs($captain)->test(CompetitionPaymentForm::class, ['competition' => 'mcc'])
        ->assertSee('Please grant Viewer access.')->set('senderName', 'Sender')
        ->set('documentsConfirmed', true)->call('submit')->assertHasNoErrors();
    $payment->refresh();
    expect($payment->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($payment->review_note)->toBeNull();
    app(VerifyCompetitionPayment::class)->handle($admin, $payment);
    expect($payment->fresh()->status)->toBe(PaymentStatus::Verified)
        ->and($payment->registration->fresh()->status)->toBe(RegistrationStatus::Verified);
});

test('a rejection of Drive documents requires a correction note', function () {
    $payment = drivePayment();
    app(ConfirmDrivePayment::class)->handle($payment->registration->team->captain, $payment, 'Sender', true);
    app(RejectCompetitionPayment::class)->handle(User::factory()->admin()->create(), $payment);
})->throws(ValidationException::class, 'Explain');

test('folder replacement by admin invalidates previous Drive approvals', function () {
    $payment = drivePayment();
    $team = $payment->registration->team;
    $admin = User::factory()->admin()->create();
    app(ConfirmDrivePayment::class)->handle($team->captain, $payment, 'Sender', true);
    app(VerifyCompetitionPayment::class)->handle($admin, $payment);
    app(UpdateDriveFolder::class)->handle($admin, $team, 'https://drive.google.com/drive/folders/replacement');
    expect($payment->fresh()->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($payment->fresh()->verified_by)->toBeNull()
        ->and($payment->registration->fresh()->status)->toBe(RegistrationStatus::Pending);
});

test('a captain cannot replace a locked folder', function () {
    $payment = drivePayment();
    $team = $payment->registration->team;
    app(ConfirmDrivePayment::class)->handle($team->captain, $payment, 'Sender', true);
    app(UpdateDriveFolder::class)->handle($team->captain, $team, 'https://drive.google.com/drive/folders/replacement');
})->throws(ValidationException::class, 'Contact the committee');

test('legacy locked teams can supply their first folder', function () {
    $team = Team::factory()->locked()->create();
    app(UpdateDriveFolder::class)->handle($team->captain, $team, 'https://drive.google.com/drive/folders/legacy');
    expect($team->fresh()->hasDocumentsFolder())->toBeTrue();
});

test('only the captain and admin can open a team folder', function () {
    $team = driveTeam();
    $url = route('dashboard.team.documents', $team);
    $this->get($url)->assertRedirect('/login');
    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
    $this->actingAs($team->captain)->get($url)->assertRedirect($team->documents_drive_url);
    $this->actingAs(User::factory()->admin()->create())->get($url)->assertRedirect($team->documents_drive_url);
});

test('payment pages are scoped to the signed in captain on every request', function () {
    $payment = drivePayment();
    $this->actingAs(User::factory()->create())->get(route('dashboard.registration.payment', 'mcc'))->assertNotFound();
});

test('members retain the maximum size and cross-account email rules without uploads', function () {
    $team = driveTeam();
    $action = app(SaveDriveTeamMember::class);
    foreach ([1, 2] as $number) {
        $action->handle($team->captain, $team, ['name' => 'Member '.$number, 'email' => "member$number@example.test", 'whatsapp' => '0812345678']);
    }
    $action->handle($team->captain, $team, ['name' => 'Extra', 'email' => 'extra@example.test', 'whatsapp' => '0812345678']);
})->throws(ValidationException::class, 'at most three');

test('member editing rejects an account email', function () {
    $team = driveTeam();
    $other = User::factory()->create();
    Livewire::actingAs($team->captain)->test(TeamManagement::class)
        ->set('memberForm.name', 'Existing account')->set('memberForm.email', $other->email)
        ->set('memberForm.whatsapp', '0812345678')->call('addMember')->assertHasErrors('memberForm.email');
    expect($team->members()->count())->toBe(0);
});

test('member IDs from another team cannot be edited and locked members cannot be changed', function () {
    $team = driveTeam();
    $other = Team::factory()->create();
    $member = TeamMember::factory()->for($other)->create();
    $this->actingAs($team->captain);
    try {
        app(SaveDriveTeamMember::class)->handle($team->captain, $team, ['name' => 'Changed', 'email' => 'changed@example.test', 'whatsapp' => '08123'], $member->id);
        $this->fail('A member from another team must be rejected.');
    } catch (ModelNotFoundException) {
        expect($member->fresh()->name)->not->toBe('Changed');
    }
    $team->lock();
    app(SaveDriveTeamMember::class)->handle($team->captain, $team, ['name' => 'New', 'email' => 'new@example.test', 'whatsapp' => '08123']);
})->throws(AuthorizationException::class);

test('admin can request a correction using the payment table action', function () {
    $payment = drivePayment();
    app(ConfirmDrivePayment::class)->handle($payment->registration->team->captain, $payment, 'Sender', true);
    Livewire::actingAs(User::factory()->admin()->create())
        ->test(ListPayments::class)
        ->callTableAction('reject', $payment, data: ['review_note' => 'The captain KTM is missing.'])
        ->assertHasNoTableActionErrors();
    expect($payment->fresh()->status)->toBe(PaymentStatus::Rejected)
        ->and($payment->fresh()->review_note)->toBe('The captain KTM is missing.');
});
