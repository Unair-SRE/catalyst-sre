<?php

namespace App\Providers;

use App\Contracts\CompetitionPaymentStorage;
use App\Contracts\KtmStorage;
use App\Services\ImageKitCompetitionPaymentStorage;
use App\Services\ImageKitKtmStorage;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
