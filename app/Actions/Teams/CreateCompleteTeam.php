<?php

namespace App\Actions\Teams;

use App\Contracts\KtmStorage;
use App\Models\Team;
use App\Models\User;
use App\Rules\AvailableTeamEmail;
use App\Support\Files\StoredPrivateFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateCompleteTeam
{
    public function __construct(private readonly KtmStorage $storage) {}

    /**
     * @param  array<string, mixed>  $teamInput
     * @param  array<int, array<string, mixed>>  $members
     */
    public function handle(
        User $captain,
        array $teamInput,
        ?UploadedFile $captainKtm,
        array $members = [],
    ): Team {
        Gate::forUser($captain)->authorize('create', Team::class);

        $members = array_values(array_map(
            fn (array $member): array => [
                ...$member,
                'email' => Str::lower(trim((string) ($member['email'] ?? ''))),
            ],
            $members,
        ));

        $validated = Validator::make([
            'team' => $teamInput,
            'captain_ktm' => $captainKtm,
            'members' => $members,
        ], [
            'team.name' => ['required', 'string', 'max:120'],
            'team.institution' => ['required', 'string', 'max:160'],
            'captain_ktm' => [
                Rule::requiredIf(! $captain->hasCompleteKtm()),
                'nullable',
                'file',
                'mimes:jpg,jpeg,png',
                'mimetypes:image/jpeg,image/png',
                'max:2048',
            ],
            'members' => ['array', 'max:2'],
            'members.*.name' => ['required', 'string', 'max:120'],
            'members.*.email' => [
                'required',
                'string',
                'email',
                'max:255',
                'distinct:ignore_case',
                new AvailableTeamEmail,
            ],
            'members.*.whatsapp' => ['required', 'string', 'max:20'],
            'members.*.ktm' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png',
                'mimetypes:image/jpeg,image/png',
                'max:2048',
            ],
        ])->validate();

        /** @var array<int, StoredPrivateFile> $memberFiles */
        $memberFiles = [];
        $captainFile = null;

        try {
            if ($captainKtm) {
                $captainFile = $this->storage->upload($captainKtm);
            }

            foreach ($validated['members'] as $index => $member) {
                $memberFiles[$index] = $this->storage->upload($member['ktm']);
            }

            $team = DB::transaction(function () use ($captain, $validated, $captainFile, $memberFiles): Team {
                $lockedCaptain = User::query()->lockForUpdate()->findOrFail($captain->id);

                if ($lockedCaptain->captainedTeam()->exists()) {
                    throw ValidationException::withMessages([
                        'team' => 'You already captain a team.',
                    ]);
                }

                $team = Team::query()->create([
                    'captain_id' => $lockedCaptain->id,
                    'name' => trim($validated['team']['name']),
                    'institution' => trim($validated['team']['institution']),
                ]);

                if ($captainFile) {
                    $lockedCaptain->update([
                        'ktm_url' => $captainFile->url,
                        'ktm_file_id' => $captainFile->fileId,
                    ]);
                }

                foreach ($validated['members'] as $index => $member) {
                    $team->members()->create([
                        'name' => trim($member['name']),
                        'email' => $member['email'],
                        'whatsapp' => trim($member['whatsapp']),
                        'ktm_url' => $memberFiles[$index]->url,
                        'ktm_file_id' => $memberFiles[$index]->fileId,
                    ]);
                }

                return $team;
            });
        } catch (Throwable $exception) {
            $this->deleteSafely($captainFile?->fileId);

            foreach ($memberFiles as $memberFile) {
                $this->deleteSafely($memberFile->fileId);
            }

            throw $exception;
        }

        $oldCaptainFileId = $captain->ktm_file_id;

        if ($captainFile && filled($oldCaptainFileId) && $oldCaptainFileId !== $captainFile->fileId) {
            $this->deleteSafely($oldCaptainFileId);
        }

        return $team->load(['captain', 'members']);
    }

    private function deleteSafely(?string $fileId): void
    {
        if (blank($fileId)) {
            return;
        }

        try {
            $this->storage->delete($fileId);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
