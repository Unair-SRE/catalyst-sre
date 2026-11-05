<?php

namespace App\Filament\Resources\QualificationResults\Tables;

use App\Actions\Results\SetQualificationResult;
use App\Enums\QualificationStatus;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class QualificationResultsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('registration.team.name')->label('Team')->searchable()->sortable(),
                TextColumn::make('registration.competition.code')->label('Competition')->badge()->sortable(),
                TextColumn::make('round.name')->label('Stage')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('decided_at')->label('Decided')->dateTime()->sortable()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('round')
                    ->relationship('round', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')->options([
                    QualificationStatus::Qualified->value => 'Qualified',
                    QualificationStatus::NotQualified->value => 'Not qualified',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    self::markBulkAction('mark-qualified', 'Mark qualified', QualificationStatus::Qualified),
                    self::markBulkAction('mark-not-qualified', 'Mark not qualified', QualificationStatus::NotQualified),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    private static function markBulkAction(string $name, string $label, QualificationStatus $status): BulkAction
    {
        return BulkAction::make($name)
            ->label($label)
            ->icon(Heroicon::OutlinedCheckBadge)
            ->requiresConfirmation()
            ->deselectRecordsAfterCompletion()
            ->action(function (Collection $records) use ($status): void {
                $succeeded = 0;
                $failed = 0;

                foreach ($records as $record) {
                    try {
                        app(SetQualificationResult::class)->handle(
                            Auth::user(),
                            $record->registration,
                            $record->round,
                            $status,
                        );
                        $succeeded++;
                    } catch (\Throwable $exception) {
                        report($exception);
                        $failed++;
                    }
                }

                Notification::make()
                    ->title("{$succeeded} result(s) recorded".($failed > 0 ? ", {$failed} skipped (see logs)" : ''))
                    ->success($failed === 0)
                    ->warning($failed > 0)
                    ->send();
            });
    }
}
