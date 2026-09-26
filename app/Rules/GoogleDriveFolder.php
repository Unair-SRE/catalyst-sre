<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleDriveFolder implements ValidationRule
{
    public static function valid(mixed $value): bool
    {
        if (! is_string($value) || strlen($value) > 2048) {
            return false;
        }

        $parts = parse_url($value);

        return $parts !== false
            && ($parts['scheme'] ?? '') === 'https'
            && strtolower($parts['host'] ?? '') === 'drive.google.com'
            && ! isset($parts['user'])
            && ! isset($parts['pass'])
            && ! isset($parts['port'])
            && ! isset($parts['fragment'])
            && preg_match('~^/drive/(?:u/\d+/)?folders/[A-Za-z0-9_-]+/?$~D', $parts['path'] ?? '') === 1;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! self::valid($value)) {
            $fail('Enter a Google Drive folder link: https://drive.google.com/drive/folders/...');
        }
    }
}
