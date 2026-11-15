<?php

namespace App\Contracts;

interface SubmissionFileInspector
{
    public function inspect(string $publicId): ?SubmissionFile;
}
