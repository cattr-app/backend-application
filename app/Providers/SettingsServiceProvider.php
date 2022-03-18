<?php

namespace App\Providers;

use App\Contracts\SettingsProvider;
use App\Services\SettingsProviderService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(
            SettingsProvider::class,
            SettingsProviderService::class
        );

        $this->app->bind('settings', function () {
            return $this->app->makeWith(SettingsProvider::class, ['saveScope' => false]);
        });
    }

    public function provides(): array
    {
        return [SettingsProvider::class, 'settings'];
    }
}
