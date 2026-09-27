<?php

namespace App\Services;

class CloudinaryStoredFile
{
    public function __construct(
        public readonly string $url,
        public readonly string $publicId,
    ) {}
}
