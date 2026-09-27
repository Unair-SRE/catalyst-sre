<?php

namespace App\Providers;

use Illuminate\Foundation\Console\ServeCommand;
use Illuminate\Foundation\DevCommands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Participant documents and payment proofs live in their respective
        // Google Drive folders. Summit ticket PDFs are rendered on demand.
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $uploadTemporaryDirectory = storage_path('app/php-upload-tmp');
        $livewireTemporaryDirectory = storage_path('framework/livewire-tmp');

        File::ensureDirectoryExists($uploadTemporaryDirectory);
        File::ensureDirectoryExists($livewireTemporaryDirectory);

        foreach (['TMP', 'TEMP', 'TMPDIR'] as $environmentVariable) {
            putenv($environmentVariable.'='.$uploadTemporaryDirectory);
            $_ENV[$environmentVariable] = $uploadTemporaryDirectory;
            $_SERVER[$environmentVariable] = $uploadTemporaryDirectory;

            if (! in_array($environmentVariable, ServeCommand::$passthroughVariables, true)) {
                ServeCommand::$passthroughVariables[] = $environmentVariable;
            }
        }

        DevCommands::register(
            'php -d upload_tmp_dir='.str_replace('\\', '/', $uploadTemporaryDirectory).' artisan serve',
            'server',
        );
    }
}
