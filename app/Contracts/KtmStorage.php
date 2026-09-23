<?php

namespace App\Contracts;

use App\Support\Files\StoredPrivateFile;
use Illuminate\Http\UploadedFile;

interface KtmStorage extends PrivateFileUrlGenerator
{
    public function upload(UploadedFile $file): StoredPrivateFile;

    public function delete(string $fileId): void;
}
