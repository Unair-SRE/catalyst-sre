<?php

namespace App\Actions\Teams;

use App\Contracts\KtmStorage;
use App\Models\TeamMember;
use App\Models\User;
use Throwable;

class DeleteTeamMemberWithKtm
{
    public function __construct(
        private readonly KtmStorage $storage,
        private readonly RemoveTeamMember $removeTeamMember,
    ) {}

    public function handle(User $actor, TeamMember $member): void
    {
        $fileId = $member->ktm_file_id;

        $this->removeTeamMember->handle($actor, $member);

        if (filled($fileId)) {
            try {
                $this->storage->delete($fileId);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
