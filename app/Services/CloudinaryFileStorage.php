<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class CloudinaryFileStorage
{
    public function upload(UploadedFile $file, string $folder): CloudinaryStoredFile
    {
        $result = $this->client()->uploadApi()->upload($file->getRealPath(), [
            'folder' => trim($folder, '/'),
            'type' => 'private',
            'use_filename' => true,
            'unique_filename' => true,
        ]);

        $url = $result['secure_url'] ?? null;
        $publicId = $result['public_id'] ?? null;

        if (! is_string($url) || ! is_string($publicId)) {
            throw new RuntimeException('Cloudinary returned an incomplete upload response.');
        }

        return new CloudinaryStoredFile($url, $publicId);
    }

    public function signedUrl(string $publicId, string $resourceType = 'image'): string
    {
        $asset = $resourceType === 'raw'
            ? $this->client()->raw($publicId)
            : $this->client()->image($publicId);

        $url = (string) $asset->deliveryType('private')->signUrl()->toUrl();

        if ($url === '') {
            throw new RuntimeException('Cloudinary could not generate a signed URL.');
        }

        return $url;
    }

    public function delete(string $publicId): void
    {
        $result = $this->client()->uploadApi()->destroy($publicId, ['type' => 'private']);

        if (! in_array($result['result'] ?? null, ['ok', 'not found'], true)) {
            throw new RuntimeException('Cloudinary could not delete the stored file.');
        }
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
