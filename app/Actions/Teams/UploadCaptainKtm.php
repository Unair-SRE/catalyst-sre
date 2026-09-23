<?php

namespace App\Actions\Teams;

use App\Contracts\KtmStorage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UploadCaptainKtm
{
    public function __construct(
        private readonly KtmStorage $storage,
        private readonly UpdateCaptainKtm $updateCaptainKtm,
    ) {}

    public function handle(User $actor, Team $team, UploadedFile $ktm): User
    {
        Validator::make(['ktm' => $ktm], [
            'ktm' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ])->validate();

        $oldFileId = $team->captain->ktm_file_id;
        $storedFile = $this->storage->upload($ktm);

        try {
            $captain = $this->updateCaptainKtm->handle($actor, $team, [
                'ktm_url' => $storedFile->url,
                'ktm_file_id' => $storedFile->fileId,
            ]);
        } catch (Throwable $exception) {
            $this->deleteSafely($storedFile->fileId);

            throw $exception;
        }

        if (filled($oldFileId) && $oldFileId !== $storedFile->fileId) {
            $this->deleteSafely($oldFileId);
        }

        return $captain;
    }

    private function deleteSafely(string $fileId): void
    {
        try {
            $this->storage->delete($fileId);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
