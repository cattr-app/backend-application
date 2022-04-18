<?php

namespace App\Providers;

use App\Helpers\VariationContainer;
use App\Services\IntervalProofs\ScreenshotService;
use Illuminate\Support\ServiceProvider;

class IntervalProofsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('interval_proof_providers', static function ($app) {
            return $app->make(VariationContainer::class)->addProviders([
                $app->make(ScreenshotService::class),
            ]);
        });
    }
}
