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
        foreach (['image', 'raw'] as $resourceType) {
            try {
                $asset = $this->client()->adminApi()->asset($publicId, [
                    'resource_type' => $resourceType,
                    'type' => 'authenticated',
                ]);
            } catch (Throwable) {
                continue;
            }

            if (! is_array($asset) && ! $asset instanceof \ArrayAccess) {
                continue;
            }

            $assetId = is_string($asset['public_id'] ?? null) ? $asset['public_id'] : $publicId;
            $format = $asset['format'] ?? null;
            // Raw uploads keep the extension in the public id and may not report a format.
            $format = is_string($format) && $format !== ''
                ? strtolower($format)
                : strtolower(pathinfo($assetId, PATHINFO_EXTENSION));
            $bytes = $asset['bytes'] ?? null;

            if ($format === '' || ! is_int($bytes)) {
                continue;
            }

            return new SubmissionFile(
                publicId: $assetId,
                format: $format,
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
