<?php

namespace Tests\Fakes;

use App\Contracts\SummitPaymentStorage;
use App\Support\Files\StoredPrivateFile;
use Closure;
use Illuminate\Http\UploadedFile;

class FakeSummitPaymentStorage implements SummitPaymentStorage
{
    /** @var array<int, string> */
    public array $uploaded = [];

    /** @var array<int, string> */
    public array $deleted = [];

    public ?Closure $afterUpload = null;

    public function upload(UploadedFile $file): StoredPrivateFile
    {
        $fileId = 'fake-'.count($this->uploaded).'-'.$file->hashName();
        $this->uploaded[] = $fileId;

        if ($this->afterUpload !== null) {
            ($this->afterUpload)();
        }

        return new StoredPrivateFile(
            'https://ik.imagekit.io/catalyst/summit-payments/'.$file->hashName(),
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
