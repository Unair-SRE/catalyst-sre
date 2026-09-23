<?php

namespace App\Contracts;

interface PrivateFileUrlGenerator
{
    public function temporaryUrl(string $url, int $expiresInSeconds = 300): string;
}
