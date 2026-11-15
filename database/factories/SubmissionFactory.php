<?php

namespace Database\Factories;

use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Submission> */
class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'competition_round_id' => CompetitionRound::factory(),
            'version' => 1,
            'public_id' => 'catalyst/submissions/'.fake()->uuid().'.pdf',
            'file_url' => 'https://res.cloudinary.com/demo/raw/upload/catalyst/submissions/'.fake()->uuid().'.pdf',
            'bytes' => fake()->numberBetween(100_000, 4_000_000),
            'format' => 'pdf',
        ];
    }
}
