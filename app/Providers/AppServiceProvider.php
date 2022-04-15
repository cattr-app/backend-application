<?php

namespace App\Providers;

use App;
use App\Http\Responses\FractalResponse;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Nuwave\Lighthouse\Support\Contracts\CreatesResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.debug') && App::environment(['local', 'staging'])) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->bind(
            App\Contracts\ScreenshotService::class,
            App\Services\ProductionScreenshotService::class
        );

        $this->app->bind(CreatesResponse::class, FractalResponse::class);
    }
}
