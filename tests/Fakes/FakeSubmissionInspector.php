<?php

namespace Tests\Fakes;

use App\Contracts\SubmissionFile;
use App\Contracts\SubmissionFileInspector;

class FakeSubmissionInspector implements SubmissionFileInspector
{
    /** @var array<string, SubmissionFile> */
    public array $files = [];

    public function inspect(string $publicId): ?SubmissionFile
    {
        return $this->files[$publicId] ?? null;
    }
}
