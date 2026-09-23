<?php

namespace App\Actions\Teams;

use App\Contracts\KtmStorage;
use App\Models\TeamMember;
use App\Models\User;
use App\Rules\AvailableTeamEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class UpdateTeamMemberWithKtm
{
    public function __construct(
        private readonly KtmStorage $storage,
        private readonly UpdateTeamMember $updateTeamMember,
    ) {}

    /** @param array<string, mixed> $input */
    public function handle(User $actor, TeamMember $member, array $input, ?UploadedFile $ktm = null): TeamMember
    {
        $input['email'] = Str::lower(trim((string) ($input['email'] ?? '')));
        $validated = Validator::make([...$input, 'ktm' => $ktm], [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', new AvailableTeamEmail($member->id)],
            'whatsapp' => ['required', 'string', 'max:20'],
            'ktm' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ])->validate();

        $storedFile = $ktm ? $this->storage->upload($ktm) : null;
        $oldFileId = $member->ktm_file_id;

        try {
            $updated = $this->updateTeamMember->handle($actor, $member, [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'whatsapp' => $validated['whatsapp'],
                'ktm_url' => $storedFile?->url ?? $member->ktm_url,
                'ktm_file_id' => $storedFile?->fileId ?? $member->ktm_file_id,
            ]);
        } catch (Throwable $exception) {
            if ($storedFile) {
                $this->deleteSafely($storedFile->fileId);
            }

            throw $exception;
        }

        if ($storedFile && filled($oldFileId) && $oldFileId !== $storedFile->fileId) {
            $this->deleteSafely($oldFileId);
        }

        return $updated;
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
