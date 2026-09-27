<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'user';

    protected static ?string $pluralModelLabel = 'Users';

    protected static ?string $recordTitleAttribute = 'name';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('role')->badge()->sortable(),
                TextColumn::make('captainedTeam.name')->label('Team')->placeholder('No team')->searchable(),
                TextColumn::make('created_at')->label('Joined')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')->options([
                    'PARTICIPANT' => 'Participant',
                    'ADMIN' => 'Admin',
                ]),
            ])
            ->recordActions([ViewAction::make()])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')->schema([
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                TextEntry::make('role')->badge(),
                TextEntry::make('email_verified_at')->label('Email verified at')->dateTime()->placeholder('Not verified'),
                TextEntry::make('created_at')->label('Joined at')->dateTime(),
            ])->columns(2),
            Section::make('Competition team')->schema([
                TextEntry::make('captainedTeam.name')->label('Team')->placeholder('No team'),
                TextEntry::make('captainedTeam.institution')->label('Institution')->placeholder('No team'),
                RepeatableEntry::make('captainedTeam.registrations')->label('Registrations')->schema([
                    TextEntry::make('competition.name')->label('Competition'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('payment.status')->label('Payment')->badge()->placeholder('Not submitted'),
                ])->columns(3)->columnSpanFull(),
            ])->columns(2),
            Section::make('Summit orders')->schema([
                RepeatableEntry::make('summitOrders')->label('')->schema([
                    TextEntry::make('id')->label('Order')->formatStateUsing(fn ($state): string => '#'.$state),
                    TextEntry::make('quantity')->label('Tickets'),
                    TextEntry::make('total_amount')->label('Total')->money('IDR'),
                    TextEntry::make('payment_status')->label('Status')->badge(),
                ])->columns(4)->contained(false),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
        ];
    }
}
