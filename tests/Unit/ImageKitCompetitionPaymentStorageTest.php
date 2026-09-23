<?php

use App\Services\ImageKitCompetitionPaymentStorage;
use Tests\TestCase;

uses(TestCase::class);

test('it generates an expiring imagekit signature without exposing the private key', function () {
    config()->set('services.imagekit.url_endpoint', 'https://ik.imagekit.io/catalyst');
    config()->set('services.imagekit.public_key', 'public-test-key');
    config()->set('services.imagekit.private_key', 'private-test-key');
    $url = 'https://ik.imagekit.io/catalyst/competition-payments/proof.jpg';

    $signedUrl = app(ImageKitCompetitionPaymentStorage::class)->temporaryUrl($url);

    expect($signedUrl)
        ->toStartWith($url.'?ik-t=')
        ->toContain('&ik-s=')
        ->not->toContain('private-test-key');
});

test('it rejects stored URLs outside the configured ImageKit endpoint', function () {
    config()->set('services.imagekit.url_endpoint', 'https://ik.imagekit.io/catalyst');

    app(ImageKitCompetitionPaymentStorage::class)
        ->temporaryUrl('https://example.com/proof.jpg');
})->throws(RuntimeException::class, 'does not belong');
