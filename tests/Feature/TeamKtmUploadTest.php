<?php

use App\Actions\Teams\DeleteTeamMemberWithKtm;
use App\Actions\Teams\UploadCaptainKtm;
use App\Contracts\KtmStorage;
use App\Livewire\Dashboard\TeamManagement;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Support\Files\StoredPrivateFile;
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

test('KTM rejects unsupported files and files larger than two megabytes', function (UploadedFile $file) {
    $team = Team::factory()->create();

    app(UploadCaptainKtm::class)->handle($team->captain, $team, $file);
})->with([
    'PDF' => fn () => UploadedFile::fake()->create('ktm.pdf', 100, 'application/pdf'),
    'too large' => fn () => UploadedFile::fake()->create('ktm.png', 2049, 'image/png'),
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

test('team management explains the two megabyte KTM limit', function () {
    $team = Team::factory()->create();

    Livewire::actingAs($team->captain)
        ->test(TeamManagement::class)
        ->set('memberKtm', UploadedFile::fake()->create('large-ktm.png', 2049, 'image/png'))
        ->assertHasErrors(['memberKtm' => 'max'])
        ->assertSee('The KTM must not exceed 2 MB.');
});

test('duplicate member email is rejected before KTM storage is called', function () {
    User::factory()->create(['email' => 'registered@example.test']);
    $team = Team::factory()->create();

    Livewire::actingAs($team->captain)
        ->test(TeamManagement::class)
        ->set('memberForm.name', 'Existing Account')
        ->set('memberForm.email', 'REGISTERED@example.test')
        ->set('memberForm.whatsapp', '081300000000')
        ->set('memberKtm', UploadedFile::fake()->create('member.png', 100, 'image/png'))
        ->call('addMember')
        ->assertHasErrors('memberForm.email')
        ->assertSee('This email is already used by an account or team member.');

    expect($this->ktmStorage->uploaded)->toBeEmpty();
    $this->assertDatabaseCount('team_members', 0);
});

test('team management reports unavailable KTM storage without creating a member', function () {
    $this->app->instance(KtmStorage::class, new class extends FakeKtmStorage
    {
        public function upload(UploadedFile $file): StoredPrivateFile
        {
            throw new RuntimeException('IMAGEKIT_PUBLIC_KEY is not configured.');
        }
    });

    $team = Team::factory()->create();

    Livewire::actingAs($team->captain)
        ->test(TeamManagement::class)
        ->set('memberForm.name', 'Storage Test')
        ->set('memberForm.email', 'storage@example.test')
        ->set('memberForm.whatsapp', '081300000000')
        ->set('memberKtm', UploadedFile::fake()->create('member.png', 100, 'image/png'))
        ->call('addMember')
        ->assertHasErrors('memberKtm')
        ->assertSee('KTM storage is not configured. Contact the Catalyst administrator before trying again.');

    $this->assertDatabaseCount('team_members', 0);
});

test('team management explains when selected KTM files are persisted', function () {
    $team = Team::factory()->create();

    Livewire::actingAs($team->captain)
        ->test(TeamManagement::class)
        ->assertSee('Selecting a file prepares a private preview.')
        ->assertSee('Save captain KTM')
        ->assertSee('The member record and KTM are saved together');
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
