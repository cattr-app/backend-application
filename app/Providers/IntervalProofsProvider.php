<?php

namespace App\Providers;

use App\Services\IntervalProofs\ScreenshotService;
use Cache;
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
        $this->app->tag(ScreenshotService::class, 'interval_proof_providers');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        $providers = cache('interval_proof_providers');

        foreach($this->app->tagged('interval_proof_providers') as $provider){
            $providerNumber = $this->app->call([$provider, 'getType']);

            $this->app->bind("interval_proof_provider_$providerNumber", $provider::class);

            $providers[$providerNumber] =  $this->app->call([$provider, 'getName']);
        }

        cache()->forever('interval_proof_providers', $providers);
    }
}
