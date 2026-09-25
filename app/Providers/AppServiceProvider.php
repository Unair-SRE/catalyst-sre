<?php

namespace App\Providers;

use App\Contracts\CompetitionPaymentStorage;
use App\Contracts\KtmStorage;
use App\Contracts\SummitPaymentStorage;
use App\Contracts\SummitTicketStorage;
use App\Services\ImageKitCompetitionPaymentStorage;
use App\Services\ImageKitKtmStorage;
use App\Services\ImageKitSummitPaymentStorage;
use App\Services\ImageKitSummitTicketStorage;
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
        $this->app->bind(CompetitionPaymentStorage::class, ImageKitCompetitionPaymentStorage::class);
        $this->app->bind(KtmStorage::class, ImageKitKtmStorage::class);
        $this->app->bind(SummitPaymentStorage::class, ImageKitSummitPaymentStorage::class);
        $this->app->bind(SummitTicketStorage::class, ImageKitSummitTicketStorage::class);
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

        File::ensureDirectoryExists($uploadTemporaryDirectory);

        DevCommands::register(
            'php -d upload_tmp_dir='.str_replace('\\', '/', $uploadTemporaryDirectory).' artisan serve',
            'server',
        );
    }
}
