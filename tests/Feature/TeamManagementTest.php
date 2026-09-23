<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Actions\Teams\AddTeamMember;
use App\Actions\Teams\CreateTeam;
use App\Actions\Teams\RemoveTeamMember;
use App\Actions\Teams\UpdateCaptainKtm;
use App\Actions\Teams\UpdateTeam;
use App\Actions\Teams\UpdateTeamMember;
use App\Filament\Resources\TeamMembers\TeamMemberResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function teamMemberData(array $overrides = []): array
{
    return [
        'name' => 'Bima Santoso',
        'email' => 'bima@example.test',
        'whatsapp' => '081300000000',
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/bima.jpg',
        'ktm_file_id' => 'ktm-bima',
        ...$overrides,
    ];
}

test('a verified participant can create exactly one team', function () {
    $captain = User::factory()->create();

    $team = app(CreateTeam::class)->handle($captain, [
        'name' => ' Catalyst Collective ',
        'institution' => ' Universitas Indonesia ',
    ]);

    expect($team->name)->toBe('Catalyst Collective')
        ->and($team->institution)->toBe('Universitas Indonesia')
        ->and($team->captain->is($captain))->toBeTrue()
        ->and($captain->captainedTeam->is($team))->toBeTrue()
        ->and($team->members)->toHaveCount(0)
        ->and($team->peopleCount())->toBe(1);

    app(CreateTeam::class)->handle($captain, [
        'name' => 'Second Team',
        'institution' => 'Universitas Indonesia',
    ]);
})->throws(AuthorizationException::class);

test('an unverified participant cannot create a team', function () {
    $captain = User::factory()->unverified()->create();

    app(CreateTeam::class)->handle($captain, [
        'name' => 'Catalyst Collective',
        'institution' => 'Universitas Indonesia',
    ]);
})->throws(AuthorizationException::class);

test('a team accepts at most two members in addition to its captain', function () {
    $team = Team::factory()->create();
    $captain = $team->captain;

    app(AddTeamMember::class)->handle($captain, $team, teamMemberData());
    app(AddTeamMember::class)->handle($captain, $team, teamMemberData([
        'name' => 'Citra Lestari',
        'email' => 'citra@example.test',
        'ktm_file_id' => 'ktm-citra',
    ]));

    expect($team->members()->count())->toBe(2)
        ->and($team->peopleCount())->toBe(3);

    app(AddTeamMember::class)->handle($captain, $team, teamMemberData([
        'name' => 'Dedi Saputra',
        'email' => 'dedi@example.test',
        'ktm_file_id' => 'ktm-dedi',
    ]));
})->throws(ValidationException::class, 'at most three people');

test('member email must be unique across users and all team members', function () {
    User::factory()->create(['email' => 'registered@example.test']);
    $firstTeam = Team::factory()->create();
    $secondTeam = Team::factory()->create();

    expect(fn () => app(AddTeamMember::class)->handle(
        $firstTeam->captain,
        $firstTeam,
        teamMemberData(['email' => 'REGISTERED@example.test']),
    ))->toThrow(ValidationException::class);

    app(AddTeamMember::class)->handle(
        $firstTeam->captain,
        $firstTeam,
        teamMemberData(['email' => 'member@example.test']),
    );

    expect(fn () => app(AddTeamMember::class)->handle(
        $secondTeam->captain,
        $secondTeam,
        teamMemberData(['email' => 'MEMBER@example.test']),
    ))->toThrow(ValidationException::class);
});

test('account registration rejects an email already used by a team member', function () {
    TeamMember::factory()->create(['email' => 'member@example.test']);

    $this->post('/register', [
        'name' => 'Existing Member',
        'email' => 'MEMBER@example.test',
        'whatsapp' => '081234567890',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('profile email change rejects an email already used by a team member', function () {
    TeamMember::factory()->create(['email' => 'member@example.test']);
    $user = User::factory()->create();

    app(UpdateUserProfileInformation::class)->update($user, [
        'name' => $user->name,
        'email' => 'MEMBER@example.test',
        'whatsapp' => $user->whatsapp,
    ]);
})->throws(ValidationException::class);

test('only the captain can manage a participant team', function () {
    $team = Team::factory()->create();
    $outsider = User::factory()->create();

    expect($outsider->can('view', $team))->toBeFalse()
        ->and($outsider->can('update', $team))->toBeFalse();

    app(UpdateTeam::class)->handle($outsider, $team, [
        'name' => 'Hijacked Team',
        'institution' => 'Unknown',
    ]);
})->throws(AuthorizationException::class);

test('captain can update and remove a member before the team is locked', function () {
    $team = Team::factory()->create();
    $member = TeamMember::factory()->for($team)->create();

    $updated = app(UpdateTeamMember::class)->handle($team->captain, $member, teamMemberData([
        'name' => ' Updated Member ',
        'email' => 'UPDATED@example.test',
    ]));

    expect($updated->name)->toBe('Updated Member')
        ->and($updated->email)->toBe('updated@example.test');

    app(RemoveTeamMember::class)->handle($team->captain, $updated);

    $this->assertDatabaseMissing('team_members', ['id' => $member->id]);
});

test('a locked team cannot be changed by its captain but admin can correct it', function () {
    $team = Team::factory()->locked()->create();
    $admin = User::factory()->admin()->create();

    expect($team->captain->can('update', $team))->toBeFalse()
        ->and($admin->can('update', $team))->toBeTrue();

    expect(fn () => app(UpdateTeam::class)->handle($team->captain, $team, [
        'name' => 'Captain Change',
        'institution' => $team->institution,
    ]))->toThrow(AuthorizationException::class);

    $updated = app(UpdateTeam::class)->handle($admin, $team, [
        'name' => 'Administrative Correction',
        'institution' => $team->institution,
    ]);

    expect($updated->name)->toBe('Administrative Correction');
});

test('team KTM completeness includes captain and every member', function () {
    $team = Team::factory()->create();

    expect($team->hasCompleteKtm())->toBeFalse();

    app(UpdateCaptainKtm::class)->handle($team->captain, $team, [
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/captain.jpg',
        'ktm_file_id' => 'ktm-captain',
    ]);

    $member = app(AddTeamMember::class)->handle($team->captain, $team, teamMemberData());

    expect($team->fresh()->hasCompleteKtm())->toBeTrue()
        ->and($member->hasCompleteKtm())->toBeTrue();
});

test('admin can access team and member filament resources', function () {
    $admin = User::factory()->admin()->create();
    $team = Team::factory()->create();
    $team->captain->update([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/captain.jpg',
        'ktm_file_id' => 'captain-preview',
    ]);
    $member = TeamMember::factory()->for($team)->create([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/member.jpg',
        'ktm_file_id' => 'member-preview',
    ]);

    $this->actingAs($admin)
        ->get(TeamResource::getUrl('index'))
        ->assertOk()
        ->assertSee(route('dashboard.private-files.captain-ktm', $team->captain));

    $this->actingAs($admin)
        ->get(TeamMemberResource::getUrl('index'))
        ->assertOk()
        ->assertSee(route('dashboard.private-files.team-member-ktm', $member));

    $this->actingAs($admin)
        ->get(TeamMemberResource::getUrl('edit', ['record' => $member]))
        ->assertOk()
        ->assertDontSee('KTM URL')
        ->assertDontSee('ImageKit file ID');
});

test('participants cannot access team and member filament resources', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->get(TeamResource::getUrl('index'))
        ->assertForbidden();

    $this->actingAs($participant)
        ->get(TeamMemberResource::getUrl('index'))
        ->assertForbidden();
});
