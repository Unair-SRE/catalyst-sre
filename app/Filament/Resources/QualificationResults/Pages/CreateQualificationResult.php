<?php

namespace App\Filament\Resources\QualificationResults\Pages;

use App\Actions\Results\SetQualificationResult;
use App\Enums\QualificationStatus;
use App\Filament\Resources\QualificationResults\QualificationResultResource;
use App\Models\CompetitionRound;
use App\Models\Registration;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateQualificationResult extends CreateRecord
{
    protected static string $resource = QualificationResultResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SetQualificationResult::class)->handle(
            Auth::user(),
            Registration::query()->findOrFail($data['registration_id']),
            CompetitionRound::query()->findOrFail($data['competition_round_id']),
            QualificationStatus::from($data['status']),
        );
    }
}
