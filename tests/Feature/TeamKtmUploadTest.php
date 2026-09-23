<?php

use App\Actions\Teams\DeleteTeamMemberWithKtm;
use App\Actions\Teams\UploadCaptainKtm;
use App\Contracts\KtmStorage;
use App\Livewire\Dashboard\TeamManagement;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\Fakes\FakeKtmStorage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->ktmStorage = new FakeKtmStorage;
    $this->app->instance(KtmStorage::class, $this->ktmStorage);
});

test('captain KTM is validated, stored privately, and old file is cleaned up', function () {
    $team = Team::factory()->create();
    $team->captain->update([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/old.jpg',
        'ktm_file_id' => 'old-captain-ktm',
    ]);

    app(UploadCaptainKtm::class)->handle(
        $team->captain,
        $team,
        UploadedFile::fake()->create('captain.png', 100, 'image/png'),
    );

    expect($team->captain->refresh()->ktm_url)->toContain('/catalyst/ktm/')
        ->and($team->captain->ktm_file_id)->toStartWith('ktm-fake-')
        ->and($this->ktmStorage->uploaded)->toHaveCount(1)
        ->and($this->ktmStorage->deleted)->toContain('old-captain-ktm');
});

test('KTM rejects unsupported files and files larger than ten megabytes', function (UploadedFile $file) {
    $team = Team::factory()->create();

    app(UploadCaptainKtm::class)->handle($team->captain, $team, $file);
})->with([
    'PDF' => fn () => UploadedFile::fake()->create('ktm.pdf', 100, 'application/pdf'),
    'too large' => fn () => UploadedFile::fake()->create('ktm.png', 10241, 'image/png'),
])->throws(ValidationException::class);

test('participant can manage real team data and member KTM from the dashboard', function () {
    $captain = User::factory()->create();

    $component = Livewire::actingAs($captain)
        ->test(TeamManagement::class)
        ->set('teamForm.name', 'Database Team')
        ->set('teamForm.institution', 'Universitas Airlangga')
        ->call('saveTeam')
        ->assertHasNoErrors()
        ->assertSee('Team created');

    $team = $captain->fresh()->captainedTeam;

    expect($team)->not->toBeNull()
        ->and($team->name)->toBe('Database Team');

    $component
        ->set('captainKtm', UploadedFile::fake()->create('captain.jpg', 100, 'image/jpeg'))
        ->call('uploadCaptainKtm')
        ->assertHasNoErrors()
        ->set('memberForm.name', 'Bima Santoso')
        ->set('memberForm.email', 'bima@example.test')
        ->set('memberForm.whatsapp', '081300000000')
        ->set('memberKtm', UploadedFile::fake()->create('bima.png', 100, 'image/png'))
        ->call('addMember')
        ->assertHasNoErrors()
        ->assertSee('Bima Santoso');

    expect($team->fresh()->hasCompleteKtm())->toBeTrue()
        ->and($team->members()->first()->ktm_file_id)->toStartWith('ktm-fake-');
});

test('removing a member also removes the stored KTM', function () {
    $team = Team::factory()->create();
    $member = TeamMember::factory()->for($team)->create(['ktm_file_id' => 'member-ktm-to-delete']);

    app(DeleteTeamMemberWithKtm::class)->handle($team->captain, $member);

    $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
    expect($this->ktmStorage->deleted)->toContain('member-ktm-to-delete');
});

test('team management page requires a verified account', function () {
    $this->get('/dashboard/team')->assertRedirect('/login');

    $user = User::factory()->unverified()->create();
    $this->actingAs($user)->get('/dashboard/team')->assertRedirect('/email/verify');

    $user->markEmailAsVerified();
    $this->get('/dashboard/team')->assertOk()->assertSee('Team Management');
});
