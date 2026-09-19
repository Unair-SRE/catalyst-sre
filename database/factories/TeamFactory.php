<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Team> */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'captain_id' => User::factory(),
            'name' => fake()->unique()->company().' Team',
            'institution' => fake()->company(),
            'locked_at' => null,
        ];
    }

    public function locked(): static
    {
        return $this->state(fn (): array => ['locked_at' => now()]);
    }
}
