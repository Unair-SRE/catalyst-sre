<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\CompetitionRound;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompetitionRound> */
class CompetitionRoundFactory extends Factory
{
    public function definition(): array
    {
        return [
            'competition_id' => Competition::factory(),
            'name' => fake()->randomElement(['Penyisihan', 'Semifinal', 'Final']),
            'round_order' => 1,
        ];
    }
}
