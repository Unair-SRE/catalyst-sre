<?php

use App\Actions\Payments\RejectCompetitionPayment;
use App\Actions\Payments\ReplaceCompetitionPaymentProof;
use App\Actions\Payments\SubmitCompetitionPayment;
use App\Actions\Payments\VerifyCompetitionPayment;
use App\Contracts\CompetitionPaymentStorage;
use App\Contracts\PrivateFileUrlGenerator;
use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\PaymentSettings\PaymentSettingResource;
use App\Livewire\Dashboard\CompetitionPaymentForm;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\Fakes\FakeCompetitionPaymentStorage;

uses(RefreshDatabase::class);

function competitionPaymentFixture(): array
{
    $team = Team::factory()->create();
    $registration = Registration::factory()->for($team)->for(Competition::factory())->create();
    $payment = $registration->payment()->create();

    return [$team, $registration, $payment];
}

beforeEach(function () {
    $storage = new FakeCompetitionPaymentStorage;
    $this->app->instance(CompetitionPaymentStorage::class, $storage);
    $this->app->instance(PrivateFileUrlGenerator::class, $storage);
    $this->paymentStorage = $storage;
});

test('a captain can submit one image proof and the team is locked', function () {
    [$team, $registration, $payment] = competitionPaymentFixture();

    $result = app(SubmitCompetitionPayment::class)->handle(
        $team->captain,
        $payment,
        ' Captain Sender ',
        UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );

    expect($result->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($result->sender_name)->toBe('Captain Sender')
        ->and($result->hasProof())->toBeTrue()
        ->and($team->fresh()->isLocked())->toBeTrue()
        ->and($registration->fresh()->status)->toBe(RegistrationStatus::Pending)
        ->and($this->paymentStorage->uploaded)->toHaveCount(1);
});

test('payment proof must be a jpg jpeg or png no larger than ten megabytes', function (UploadedFile $file) {
    [$team, , $payment] = competitionPaymentFixture();

    app(SubmitCompetitionPayment::class)->handle($team->captain, $payment, 'Sender', $file);
})->with([
    'pdf' => fn () => UploadedFile::fake()->create('proof.pdf', 100, 'application/pdf'),
    'too large' => fn () => UploadedFile::fake()->create('proof.png', 10241, 'image/png'),
])->throws(ValidationException::class);

test('a participant cannot submit another team payment', function () {
    [, , $payment] = competitionPaymentFixture();
    $outsider = User::factory()->create();

    app(SubmitCompetitionPayment::class)->handle(
        $outsider,
        $payment,
        'Sender',
        UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );
})->throws(AuthorizationException::class);

test('participants cannot upload a second proof including after rejection', function () {
    [$team, , $payment] = competitionPaymentFixture();
    $payment->update([
        'sender_name' => 'Sender',
        'payment_proof_url' => 'https://example.test/proof.jpg',
        'payment_proof_file_id' => 'existing-proof',
        'status' => PaymentStatus::Rejected,
    ]);

    app(SubmitCompetitionPayment::class)->handle(
        $team->captain,
        $payment,
        'Sender',
        UploadedFile::fake()->create('new-proof.jpg', 100, 'image/jpeg'),
    );
})->throws(ValidationException::class, 'already been submitted');

test('a newly uploaded file is cleaned up when the database update cannot be completed', function () {
    [$team, , $payment] = competitionPaymentFixture();
    $this->paymentStorage->afterUpload = function () use ($payment): void {
        $payment->update([
            'sender_name' => 'Concurrent Sender',
            'payment_proof_url' => 'https://example.test/concurrent.jpg',
            'payment_proof_file_id' => 'concurrent-proof',
            'status' => PaymentStatus::WaitingVerification,
        ]);
    };

    try {
        app(SubmitCompetitionPayment::class)->handle(
            $team->captain,
            $payment,
            'Sender',
            UploadedFile::fake()->create('new-proof.jpg', 100, 'image/jpeg'),
        );
    } catch (ValidationException) {
        // The concurrently persisted proof wins and the orphan upload must be removed.
    }

    expect($this->paymentStorage->uploaded)->toHaveCount(1)
        ->and($this->paymentStorage->deleted)->toBe($this->paymentStorage->uploaded)
        ->and($payment->fresh()->payment_proof_file_id)->toBe('concurrent-proof');
});

test('admin approval atomically verifies the payment and registration and is idempotent', function () {
    [, $registration, $payment] = competitionPaymentFixture();
    $admin = User::factory()->admin()->create();
    $payment->update([
        'sender_name' => 'Sender',
        'payment_proof_url' => 'https://example.test/proof.jpg',
        'payment_proof_file_id' => 'proof-id',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    $first = app(VerifyCompetitionPayment::class)->handle($admin, $payment);
    $verifiedAt = $first->verified_at;
    $second = app(VerifyCompetitionPayment::class)->handle($admin, $first);

    expect($second->status)->toBe(PaymentStatus::Verified)
        ->and($second->verified_by)->toBe($admin->id)
        ->and($second->verified_at->equalTo($verifiedAt))->toBeTrue()
        ->and($registration->fresh()->status)->toBe(RegistrationStatus::Verified);
});

test('admin rejection updates payment and registration together', function () {
    [, $registration, $payment] = competitionPaymentFixture();
    $admin = User::factory()->admin()->create();
    $payment->update([
        'sender_name' => 'Sender',
        'payment_proof_url' => 'https://example.test/proof.jpg',
        'payment_proof_file_id' => 'proof-id',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    $result = app(RejectCompetitionPayment::class)->handle($admin, $payment);

    expect($result->status)->toBe(PaymentStatus::Rejected)
        ->and($result->verified_by)->toBe($admin->id)
        ->and($result->verified_at)->not->toBeNull()
        ->and($registration->fresh()->status)->toBe(RegistrationStatus::Rejected);
});

test('participants cannot approve or reject payments', function () {
    [$team, , $payment] = competitionPaymentFixture();
    $payment->update([
        'sender_name' => 'Sender',
        'payment_proof_url' => 'https://example.test/proof.jpg',
        'payment_proof_file_id' => 'proof-id',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    expect(fn () => app(VerifyCompetitionPayment::class)->handle($team->captain, $payment))
        ->toThrow(AuthorizationException::class)
        ->and(fn () => app(RejectCompetitionPayment::class)->handle($team->captain, $payment))
        ->toThrow(AuthorizationException::class);
});

test('admin can replace a rejected proof and return it to verification', function () {
    [, $registration, $payment] = competitionPaymentFixture();
    $admin = User::factory()->admin()->create();
    $payment->update([
        'sender_name' => 'Old Sender',
        'payment_proof_url' => 'https://example.test/old.jpg',
        'payment_proof_file_id' => 'old-proof',
        'status' => PaymentStatus::Rejected,
        'verified_by' => $admin->id,
        'verified_at' => now(),
    ]);
    $registration->update(['status' => RegistrationStatus::Rejected]);

    $result = app(ReplaceCompetitionPaymentProof::class)->handle(
        $admin,
        $payment,
        'Correct Sender',
        UploadedFile::fake()->create('corrected.png', 100, 'image/png'),
    );

    expect($result->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($result->sender_name)->toBe('Correct Sender')
        ->and($result->verified_by)->toBeNull()
        ->and($result->verified_at)->toBeNull()
        ->and($registration->fresh()->status)->toBe(RegistrationStatus::Pending)
        ->and($this->paymentStorage->deleted)->toContain('old-proof');
});

test('database enforces one payment for each registration', function () {
    [, $registration] = competitionPaymentFixture();

    Payment::query()->create(['registration_id' => $registration->id]);
})->throws(QueryException::class);

test('only the owner and admin can open a private payment proof', function () {
    [$team, , $payment] = competitionPaymentFixture();
    $payment->update([
        'payment_proof_url' => 'https://ik.imagekit.io/catalyst/competition-payments/proof.jpg',
        'payment_proof_file_id' => 'proof-id',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    $this->actingAs($team->captain)
        ->get(route('dashboard.competition-payment.proof', $payment))
        ->assertRedirectContains('signed=fake');

    $this->actingAs(User::factory()->create())
        ->get(route('dashboard.competition-payment.proof', $payment))
        ->assertForbidden();
});

test('admin can access competition payments resource but participants cannot', function () {
    $admin = User::factory()->admin()->create();
    [, , $payment] = competitionPaymentFixture();
    $payment->update([
        'sender_name' => 'Preview Sender',
        'payment_proof_url' => 'https://ik.imagekit.io/catalyst/competition-payments/preview.jpg',
        'payment_proof_file_id' => 'preview-proof',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    $this->actingAs($admin)
        ->get(PaymentResource::getUrl('index'))
        ->assertOk()
        ->assertSee(route('dashboard.competition-payment.proof', $payment));
    $this->actingAs(User::factory()->create())->get(PaymentResource::getUrl('index'))->assertForbidden();
});

test('payment details render the proof preview and business metadata', function () {
    [, , $payment] = competitionPaymentFixture();
    $payment->update([
        'sender_name' => 'Preview Sender',
        'payment_proof_url' => 'https://ik.imagekit.io/catalyst/competition-payments/preview.jpg',
        'payment_proof_file_id' => 'preview-proof',
        'status' => PaymentStatus::WaitingVerification,
    ]);

    $this->view('payment-details', [
        'payment' => $payment->load(['registration.team.captain', 'registration.competition', 'verifier']),
        'proofUrl' => route('dashboard.competition-payment.proof', $payment),
    ])
        ->assertSee('Preview Sender')
        ->assertSee('preview-proof')
        ->assertSee(route('dashboard.competition-payment.proof', $payment));
});

test('payment settings displays the static qris without an editable qris url', function () {
    $admin = User::factory()->admin()->create();
    $setting = PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst CP',
        'is_active' => true,
    ]);

    $this->actingAs($admin)
        ->get(PaymentSettingResource::getUrl('edit', ['record' => $setting]))
        ->assertOk()
        ->assertSee(asset(config('services.catalyst.qris_asset')))
        ->assertSee('Static QRIS')
        ->assertDontSee('QRIS URL');
});

test('the participant payment page displays active qris and submits the proof', function () {
    $team = Team::factory()->create();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();
    $registration = Registration::factory()->for($team)->for($competition)->create();
    $payment = $registration->payment()->create();
    PaymentSetting::query()->create([
        'qris_url' => 'https://example.test/legacy-qris.png',
        'contact_person_name' => 'Catalyst CP',
        'contact_person_whatsapp' => '628123456789',
        'is_active' => true,
    ]);

    $this->actingAs($team->captain)
        ->get(route('dashboard.registration.payment', 'mcc'))
        ->assertOk()
        ->assertSee(asset('images/payment/qris-catalyst.png'))
        ->assertDontSee('legacy-qris.png');

    Livewire::actingAs($team->captain)
        ->test(CompetitionPaymentForm::class, ['competition' => 'mcc'])
        ->set('senderName', 'Captain Sender')
        ->set('proof', UploadedFile::fake()->create('proof.png', 100, 'image/png'))
        ->call('submit')
        ->assertHasNoErrors()
        ->assertSee('Payment proof submitted');

    expect($payment->fresh()->status)->toBe(PaymentStatus::WaitingVerification)
        ->and($team->fresh()->isLocked())->toBeTrue();
});

test('the static qris asset exists in the public application assets', function () {
    expect(public_path(config('services.catalyst.qris_asset')))->toBeFile();
});

test('captain and member ktm files are private to the owner and admin', function () {
    $team = Team::factory()->create();
    $team->captain->update([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/captain.jpg',
        'ktm_file_id' => 'captain-file',
    ]);
    $member = TeamMember::factory()->for($team)->create([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/member.jpg',
        'ktm_file_id' => 'member-file',
    ]);

    $this->actingAs($team->captain)
        ->get(route('dashboard.private-files.captain-ktm', $team->captain))
        ->assertRedirectContains('signed=fake');
    $this->get(route('dashboard.private-files.team-member-ktm', $member))
        ->assertRedirectContains('signed=fake');

    $outsider = User::factory()->create();
    $this->actingAs($outsider)
        ->get(route('dashboard.private-files.captain-ktm', $team->captain))
        ->assertForbidden();
    $this->get(route('dashboard.private-files.team-member-ktm', $member))
        ->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('dashboard.private-files.team-member-ktm', $member))
        ->assertRedirectContains('signed=fake');
});

test('only the latest activated payment setting remains active', function () {
    $first = PaymentSetting::query()->create(['is_active' => true]);
    $second = PaymentSetting::query()->create(['is_active' => true]);

    expect($first->fresh()->is_active)->toBeFalse()
        ->and($second->fresh()->is_active)->toBeTrue()
        ->and(PaymentSetting::query()->where('is_active', true)->count())->toBe(1);
});
