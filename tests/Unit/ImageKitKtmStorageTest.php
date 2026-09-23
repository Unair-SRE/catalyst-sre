<?php

use App\Services\ImageKitKtmStorage;
use Tests\TestCase;

uses(TestCase::class);

test('it generates an expiring KTM URL without exposing the private key', function () {
    config()->set('services.imagekit.url_endpoint', 'https://ik.imagekit.io/catalyst');
    config()->set('services.imagekit.public_key', 'public-test-key');
    config()->set('services.imagekit.private_key', 'private-test-key');
    $url = 'https://ik.imagekit.io/catalyst/ktm/student.jpg';

    $signedUrl = app(ImageKitKtmStorage::class)->temporaryUrl($url);

    expect($signedUrl)
        ->toStartWith($url.'?ik-t=')
        ->toContain('&ik-s=')
        ->not->toContain('private-test-key');
});

test('it rejects KTM URLs outside the configured ImageKit endpoint', function () {
    config()->set('services.imagekit.url_endpoint', 'https://ik.imagekit.io/catalyst');

    app(ImageKitKtmStorage::class)->temporaryUrl('https://example.com/student.jpg');
})->throws(RuntimeException::class, 'does not belong');
