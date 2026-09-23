<?php

namespace App\Actions\Teams;

use App\Contracts\KtmStorage;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Rules\AvailableTeamEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Throwable;

class CreateTeamMemberWithKtm
{
    public function __construct(
        private readonly KtmStorage $storage,
        private readonly AddTeamMember $addTeamMember,
    ) {}

    /** @param array<string, mixed> $input */
    public function handle(User $actor, Team $team, array $input, UploadedFile $ktm): TeamMember
    {
        $input['email'] = Str::lower(trim((string) ($input['email'] ?? '')));
        $validated = Validator::make([...$input, 'ktm' => $ktm], [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', new AvailableTeamEmail],
            'whatsapp' => ['required', 'string', 'max:20'],
            'ktm' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:10240'],
        ])->validate();

        $storedFile = $this->storage->upload($ktm);

        try {
            return $this->addTeamMember->handle($actor, $team, [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'whatsapp' => $validated['whatsapp'],
                'ktm_url' => $storedFile->url,
                'ktm_file_id' => $storedFile->fileId,
            ]);
        } catch (Throwable $exception) {
            try {
                $this->storage->delete($storedFile->fileId);
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }
}
