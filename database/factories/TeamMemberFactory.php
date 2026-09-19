<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TeamMember> */
class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'whatsapp' => fake()->numerify('08##########'),
            'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/'.fake()->uuid().'.jpg',
            'ktm_file_id' => fake()->uuid(),
        ];
    }
}
