<?php

namespace App\Filament\Resources\Registrations;

use App\Enums\RegistrationStatus;
use App\Filament\Resources\Registrations\Pages\ListRegistrations;
use App\Models\Registration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('team.name')->label('Team')->searchable()->sortable(),
                TextColumn::make('team.institution')->label('Institution')->searchable(),
                TextColumn::make('team.captain.name')->label('Captain')->searchable(),
                TextColumn::make('competition.code')->label('Competition')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('created_at')->label('Registered at')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('competition')
                    ->relationship('competition', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')->options([
                    RegistrationStatus::Pending->value => 'Pending',
                    RegistrationStatus::Verified->value => 'Verified',
                    RegistrationStatus::Rejected->value => 'Rejected',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
        ];
    }
}
