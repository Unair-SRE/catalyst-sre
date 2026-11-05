<?php

namespace App\Filament\Resources\QualificationResults\Schemas;

use App\Enums\QualificationStatus;
use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\Registration;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class QualificationResultForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('registration_id')
                ->label('Registration')
                ->options(
                    Registration::query()
                        ->with(['team', 'competition'])
                        ->where('status', RegistrationStatus::Verified)
                        ->get()
                        ->mapWithKeys(fn (Registration $registration): array => [
                            $registration->id => "{$registration->team->name} — {$registration->competition->name}",
                        ])
                        ->all()
                )
                ->required()
                ->searchable(),
            Select::make('competition_round_id')
                ->label('Stage')
                ->options(
                    CompetitionRound::query()
                        ->with('competition')
                        ->orderBy('competition_id')
                        ->orderBy('round_order')
                        ->get()
                        ->mapWithKeys(fn (CompetitionRound $round): array => [
                            $round->id => "{$round->competition->name} — {$round->name}",
                        ])
                        ->all()
                )
                ->required()
                ->searchable(),
            Select::make('status')
                ->options([
                    QualificationStatus::Qualified->value => 'Qualified',
                    QualificationStatus::NotQualified->value => 'Not qualified',
                ])
                ->required(),
        ]);
    }
}
