<?php

namespace App\Services;

use App\Contracts\SummitTicketStorage;
use App\Support\Files\StoredPrivateFile;
use ImageKit\ImageKit;
use RuntimeException;

class ImageKitSummitTicketStorage implements SummitTicketStorage
{
    public function store(string $fileName, string $contents): StoredPrivateFile
    {
        $response = $this->client()->uploadFile([
            'file' => base64_encode($contents),
            'fileName' => $fileName,
            'folder' => '/catalyst/summit-tickets',
            'isPrivateFile' => true,
            'useUniqueFileName' => true,
        ]);

        $this->ensureSuccessful($response, 'upload');

        $url = $response->result->url ?? null;
        $fileId = $response->result->fileId ?? null;

        if (! is_string($url) || ! is_string($fileId)) {
            throw new RuntimeException('ImageKit returned an incomplete upload response.');
        }

        return new StoredPrivateFile($url, $fileId);
    }

    public function delete(string $fileId): void
    {
        $response = $this->client()->deleteFile($fileId);

        $this->ensureSuccessful($response, 'delete');
    }

    public function temporaryUrl(string $url, int $expiresInSeconds = 300): string
    {
        $urlEndpoint = rtrim((string) config('services.imagekit.url_endpoint'), '/').'/';

        if ($urlEndpoint === '/' || ! str_starts_with($url, $urlEndpoint)) {
            throw new RuntimeException('The stored URL does not belong to the configured ImageKit endpoint.');
        }

        $relativePath = substr($url, strlen($urlEndpoint));
        $signedUrl = $this->client()->url([
            'path' => $relativePath,
            'signed' => true,
            'expireSeconds' => $expiresInSeconds,
        ]);

        if (! is_string($signedUrl) || ! str_contains($signedUrl, 'ik-s=')) {
            throw new RuntimeException('ImageKit could not generate a signed URL.');
        }

        return $signedUrl;
    }

    private function client(): ImageKit
    {
        return new ImageKit(
            $this->configValue('public_key', 'IMAGEKIT_PUBLIC_KEY'),
            $this->configValue('private_key', 'IMAGEKIT_PRIVATE_KEY'),
            $this->configValue('url_endpoint', 'IMAGEKIT_URL_ENDPOINT'),
        );
    }

    private function configValue(string $key, string $environmentName): string
    {
        $value = config('services.imagekit.'.$key);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException($environmentName.' is not configured.');
        }

        return $value;
    }

    private function ensureSuccessful(object $response, string $operation): void
    {
        if (($response->error ?? null) === null) {
            return;
        }

        $error = $response->error;
        $message = is_object($error) && isset($error->message)
            ? $error->message
            : (is_string($error) ? $error : json_encode($error));

        throw new RuntimeException(
            'ImageKit '.$operation.' failed'.($message ? ': '.$message : '.'),
        );
    }
}
