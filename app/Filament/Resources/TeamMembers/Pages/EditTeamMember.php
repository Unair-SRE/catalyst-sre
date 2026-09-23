<?php

namespace App\Filament\Resources\TeamMembers\Pages;

use App\Filament\Resources\TeamMembers\TeamMemberResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamMemberResource::class;

    /** @param array<string, mixed> $data */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['name'] = trim($data['name']);
        $data['email'] = Str::lower(trim($data['email']));
        $data['whatsapp'] = trim($data['whatsapp']);

        return $data;
    }
}
