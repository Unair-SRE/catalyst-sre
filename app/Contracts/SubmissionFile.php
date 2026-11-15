<?php

namespace App\Contracts;

class SubmissionFile
{
    public function __construct(
        public readonly string $publicId,
        public readonly string $folder,
        public readonly string $format,
        public readonly int $bytes,
        public readonly string $url,
        public readonly ?string $etag,
    ) {}
}
