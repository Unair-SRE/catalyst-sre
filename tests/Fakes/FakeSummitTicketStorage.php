<?php

namespace Tests\Fakes;

use App\Contracts\SummitTicketStorage;
use App\Support\Files\StoredPrivateFile;

class FakeSummitTicketStorage implements SummitTicketStorage
{
    /** @var array<int, string> */
    public array $stored = [];

    /** @var array<int, string> */
    public array $deleted = [];

    public function store(string $fileName, string $contents): StoredPrivateFile
    {
        $fileId = 'fake-'.count($this->stored).'-'.$fileName;
        $this->stored[] = $fileId;

        return new StoredPrivateFile(
            'https://ik.imagekit.io/catalyst/summit-tickets/'.$fileName,
            $fileId,
        );
    }

    public function delete(string $fileId): void
    {
        $this->deleted[] = $fileId;
    }

    public function temporaryUrl(string $url, int $expiresInSeconds = 300): string
    {
        return $url.'?signed=fake&expires='.$expiresInSeconds;
    }
}
