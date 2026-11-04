<?php

namespace Database\Factories;

use App\Enums\QualificationStatus;
use App\Models\CompetitionRound;
use App\Models\QualificationResult;
use App\Models\Registration;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QualificationResult> */
class QualificationResultFactory extends Factory
{
    public function definition(): array
    {
        return [
            'registration_id' => Registration::factory(),
            'competition_round_id' => CompetitionRound::factory(),
            'status' => QualificationStatus::Qualified,
        ];
    }
}
