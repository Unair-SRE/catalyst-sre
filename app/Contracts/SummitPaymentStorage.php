<?php

namespace App\Contracts;

use App\Support\Files\StoredPrivateFile;
use Illuminate\Http\UploadedFile;

interface SummitPaymentStorage extends PrivateFileUrlGenerator
{
    public function upload(UploadedFile $file): StoredPrivateFile;

    public function delete(string $fileId): void;
}
