<?php

namespace App\Filament\Resources\Registrations;

use App\Enums\RegistrationStatus;
use App\Filament\Resources\Registrations\Pages\ListRegistrations;
use App\Filament\Resources\Registrations\Pages\ViewRegistration;
use App\Models\Registration;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 4;

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
                TextColumn::make('team.captain.name')->label('Captain')->searchable(),
                TextColumn::make('competition.code')->label('Competition')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('payment.status')->label('Payment')->badge()->placeholder('Not submitted'),
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
            ])
            ->recordActions([ViewAction::make()]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Registration')->schema([
                TextEntry::make('team.name')->label('Team'),
                TextEntry::make('competition.name')->label('Competition'),
                TextEntry::make('status')->badge(),
                TextEntry::make('created_at')->label('Registered at')->dateTime(),
                TextEntry::make('updated_at')->label('Last updated')->dateTime(),
            ])->columns(2),
            Section::make('Team and captain')->schema([
                TextEntry::make('team.institution')->label('Institution'),
                TextEntry::make('team.captain.name')->label('Captain'),
                TextEntry::make('team.captain.email')->label('Captain email')->copyable(),
                TextEntry::make('team.captain.whatsapp')->label('Captain WhatsApp')->copyable(),
                RepeatableEntry::make('team.members')->label('Members')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                ])->columns(3)->columnSpanFull(),
            ])->columns(2),
            Section::make('Payment')->schema([
                TextEntry::make('payment.status')->label('Status')->badge()->placeholder('Not submitted'),
                TextEntry::make('payment.sender_name')->label('Sender')->placeholder('Not submitted'),
                TextEntry::make('payment.documents_submitted_at')->label('Submitted at')->dateTime()->placeholder('Not submitted'),
                TextEntry::make('payment.verified_at')->label('Verified at')->dateTime()->placeholder('Not verified'),
                TextEntry::make('payment.verifier.name')->label('Reviewed by')->placeholder('Not reviewed'),
                TextEntry::make('payment.review_note')->label('Review note')->placeholder('No review note')->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRegistrations::route('/'),
            'view' => ViewRegistration::route('/{record}'),
        ];
    }
}
