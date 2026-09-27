<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Actions\Teams\UpdateDriveFolder;
use App\Filament\Resources\Teams\TeamResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return DB::transaction(function () use ($record, $data): Model {
            $record = $record->newQuery()->lockForUpdate()->findOrFail($record->id);
            $folder = trim($data['documents_drive_url'] ?? '');
            if ($folder !== ($record->documents_drive_url ?? '')) {
                if ($folder === '') {
                    throw ValidationException::withMessages(['data.documents_drive_url' => 'Replace the folder with a valid link instead of removing it.']);
                }
                app(UpdateDriveFolder::class)->handle(Auth::user(), $record, $folder, true);
            }
            $record->update(['name' => trim($data['name']), 'institution' => trim($data['institution'])]);

            return $record->refresh();
        });
    }
}
