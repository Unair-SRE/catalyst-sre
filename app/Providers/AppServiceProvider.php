<?php

namespace App\Providers;

use App\Contracts\CompetitionPaymentStorage;
use App\Contracts\PrivateFileUrlGenerator;
use App\Services\ImageKitCompetitionPaymentStorage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CompetitionPaymentStorage::class, ImageKitCompetitionPaymentStorage::class);
        $this->app->bind(PrivateFileUrlGenerator::class, ImageKitCompetitionPaymentStorage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
