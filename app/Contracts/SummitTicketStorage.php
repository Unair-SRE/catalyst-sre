<?php

namespace App\Contracts;

use App\Support\Files\StoredPrivateFile;

interface SummitTicketStorage extends PrivateFileUrlGenerator
{
    public function store(string $fileName, string $contents): StoredPrivateFile;

    public function delete(string $fileId): void;
}
