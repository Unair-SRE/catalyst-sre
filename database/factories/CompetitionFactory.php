<?php

namespace Database\Factories;

use App\Enums\CompetitionCode;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Competition> */
class CompetitionFactory extends Factory
{
    public function definition(): array
    {
        $code = fake()->randomElement(CompetitionCode::cases());

        return [
            'code' => $code,
            'name' => $code->label(),
            'description' => fake()->sentence(),
            'registration_fee' => fake()->numberBetween(75, 200) * 1000,
            'registration_open' => true,
            'registration_start_at' => now()->subDay(),
            'registration_end_at' => now()->addMonth(),
        ];
    }

    public function code(CompetitionCode $code): static
    {
        return $this->state(fn (): array => [
            'code' => $code,
            'name' => $code->label(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => ['registration_open' => false]);
    }
}
