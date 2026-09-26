<?php

use Illuminate\Foundation\Console\ServeCommand;
use Tests\TestCase;

uses(TestCase::class);

test('development server processes inherit a writable PHP upload directory', function () {
    $directory = storage_path('app/php-upload-tmp');

    expect($directory)->toBeDirectory()->toBeWritableDirectory()
        ->and(getenv('TMP'))->toBe($directory)
        ->and(getenv('TEMP'))->toBe($directory)
        ->and(getenv('TMPDIR'))->toBe($directory)
        ->and($_ENV['TMP'])->toBe($directory)
        ->and($_ENV['TEMP'])->toBe($directory)
        ->and($_ENV['TMPDIR'])->toBe($directory)
        ->and(ServeCommand::$passthroughVariables)->toContain('TMP', 'TEMP', 'TMPDIR');
});

test('livewire temporary uploads use a dedicated writable disk', function () {
    expect(config('livewire.temporary_file_upload.disk'))->toBe('livewire-tmp')
        ->and(config('filesystems.disks.livewire-tmp.root'))->toBe(storage_path('framework/livewire-tmp'))
        ->and(storage_path('framework/livewire-tmp'))->toBeDirectory()->toBeWritableDirectory();
});
