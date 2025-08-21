<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\AccountNumberHelper;
use App\Helpers\GenerationCommissionHelper;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the AccountNumberHelper as a singleton
        $this->app->singleton(AccountNumberHelper::class, function ($app) {
            return new AccountNumberHelper();
        });

        // Register the GenerationCommissionHelper as a singleton
        $this->app->singleton(GenerationCommissionHelper::class, function ($app) {
            return new GenerationCommissionHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
