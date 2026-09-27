<?php

use App\Services\CloudinaryFileStorage;
use Tests\TestCase;

uses(TestCase::class);

test('it refuses to build a client without credentials', function () {
    config()->set('services.cloudinary.cloud_name', null);
    config()->set('services.cloudinary.api_key', null);
    config()->set('services.cloudinary.api_secret', null);

    app(CloudinaryFileStorage::class)->signedUrl('catalyst/ktm/probe');
})->throws(RuntimeException::class, 'CLOUDINARY_CLOUD_NAME is not configured');

test('it builds a signed private url without network access', function () {
    config()->set('services.cloudinary.cloud_name', 'demo');
    config()->set('services.cloudinary.api_key', 'key');
    config()->set('services.cloudinary.api_secret', 'secret');

    $url = app(CloudinaryFileStorage::class)->signedUrl('catalyst/ktm/probe');

    expect($url)
        ->toContain('/image/private/s--')
        ->toContain('catalyst/ktm/probe')
        ->not->toContain('secret');
});
