<?php

namespace App\Services;

use App\Contracts\SubmissionFile;
use App\Contracts\SubmissionFileInspector;
use Cloudinary\Cloudinary;
use RuntimeException;
use Throwable;

class CloudinarySubmissionInspector implements SubmissionFileInspector
{
    public function inspect(string $publicId): ?SubmissionFile
    {
        foreach (['raw', 'image'] as $resourceType) {
            try {
                $asset = $this->client()->adminApi()->asset($publicId, [
                    'resource_type' => $resourceType,
                    'type' => 'private',
                ]);
            } catch (Throwable) {
                continue;
            }

            if (! is_array($asset) && ! $asset instanceof \ArrayAccess) {
                continue;
            }

            $folder = $asset['folder'] ?? null;
            $format = $asset['format'] ?? null;
            $bytes = $asset['bytes'] ?? null;

            if (! is_string($folder) || ! is_string($format) || ! is_int($bytes)) {
                continue;
            }

            return new SubmissionFile(
                publicId: (string) ($asset['public_id'] ?? $publicId),
                folder: $folder,
                format: strtolower($format),
                bytes: $bytes,
                url: (string) ($asset['secure_url'] ?? ''),
                etag: isset($asset['etag']) && is_string($asset['etag']) ? $asset['etag'] : null,
            );
        }

        return null;
    }

    private function client(): Cloudinary
    {
        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $this->configValue('cloud_name', 'CLOUDINARY_CLOUD_NAME'),
                'api_key' => $this->configValue('api_key', 'CLOUDINARY_API_KEY'),
                'api_secret' => $this->configValue('api_secret', 'CLOUDINARY_API_SECRET'),
            ],
            'url' => ['secure' => true],
        ]);
    }

    private function configValue(string $key, string $environmentName): string
    {
        $value = config('services.cloudinary.'.$key);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException($environmentName.' is not configured.');
        }

        return $value;
    }
}
