<?php

namespace Tests\Fakes;

use App\Contracts\KtmStorage;
use App\Support\Files\StoredPrivateFile;
use Illuminate\Http\UploadedFile;

class FakeKtmStorage implements KtmStorage
{
    /** @var array<int, string> */
    public array $uploaded = [];

    /** @var array<int, string> */
    public array $deleted = [];

    public function upload(UploadedFile $file): StoredPrivateFile
    {
        $fileId = 'ktm-fake-'.count($this->uploaded).'-'.$file->hashName();
        $this->uploaded[] = $fileId;

        return new StoredPrivateFile(
            'https://ik.imagekit.io/catalyst/ktm/'.$file->hashName(),
            $fileId,
        );
    }

    public function delete(string $fileId): void
    {
        $this->deleted[] = $fileId;
    }

    public function temporaryUrl(string $url, int $expiresInSeconds = 300): string
    {
        return $url.'?signed=ktm-fake&expires='.$expiresInSeconds;
    }
}
