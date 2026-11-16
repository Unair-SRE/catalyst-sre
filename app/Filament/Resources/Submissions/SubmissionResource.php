<?php

namespace App\Filament\Resources\Submissions;

use App\Filament\Resources\Submissions\Pages\ListSubmissions;
use App\Filament\Resources\Submissions\Pages\ViewSubmission;
use App\Models\Submission;
use App\Services\CloudinaryFileStorage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Submissions';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('registration.team.name')->label('Team')->searchable()->sortable(),
            TextColumn::make('registration.competition.code')->label('Competition')->badge()->sortable(),
            TextColumn::make('round.name')->label('Stage')->sortable(),
            TextColumn::make('version')->label('Version')->sortable(),
            TextColumn::make('format')->label('Format')->badge()->formatStateUsing(fn (string $state): string => strtoupper($state)),
            TextColumn::make('bytes')->label('Size')->formatStateUsing(fn (int $state): string => static::sizeLabel($state)),
            TextColumn::make('submitted_at')->label('Submitted')->dateTime()->timezone('Asia/Jakarta')->sortable(),
            TextColumn::make('submitter.name')->label('Submitted by')->placeholder('—'),
        ])->filters([
            SelectFilter::make('competition')
                ->label('Competition')
                ->relationship('registration.competition', 'name')
                ->searchable()
                ->preload(),
            SelectFilter::make('round')
                ->label('Stage')
                ->relationship('round', 'name')
                ->searchable()
                ->preload(),
        ])->recordActions([
            ViewAction::make(),
            static::openFileAction(),
        ])->defaultSort('submitted_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Submission')->schema([
                TextEntry::make('registration.team.name')->label('Team'),
                TextEntry::make('registration.competition.name')->label('Competition'),
                TextEntry::make('round.name')->label('Stage'),
                TextEntry::make('version')->label('Version'),
                TextEntry::make('format')->label('Format')->badge()->formatStateUsing(fn (string $state): string => strtoupper($state)),
                TextEntry::make('bytes')->label('Size')->formatStateUsing(fn (int $state): string => static::sizeLabel($state)),
                TextEntry::make('submitted_at')->label('Submitted at')->dateTime()->timezone('Asia/Jakarta'),
                TextEntry::make('submitter.name')->label('Submitted by')->placeholder('—'),
                TextEntry::make('public_id')->label('Cloudinary public id')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubmissions::route('/'),
            'view' => ViewSubmission::route('/{record}'),
        ];
    }

    public static function openFileAction(): Action
    {
        return Action::make('openFile')
            ->label('Open file')
            ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
            ->url(fn (Submission $record): string => app(CloudinaryFileStorage::class)->downloadUrl(
                $record->public_id,
                $record->format,
                $record->format === 'pdf' ? 'image' : 'raw',
            ))
            ->openUrlInNewTab();
    }

    private static function sizeLabel(int $bytes): string
    {
        return $bytes >= 1024 * 1024
            ? round($bytes / 1024 / 1024, 1).' MB'
            : max(1, (int) round($bytes / 1024)).' KB';
    }
}
