<?php

namespace App\Support\Files;

final readonly class StoredPrivateFile
{
    public function __construct(
        public string $url,
        public string $fileId,
    ) {}
}
