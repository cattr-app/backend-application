<?php

namespace App\Providers;

use App;
use App\Http\Responses\FractalResponse;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\ServiceProvider;
use Laravel\Telescope\Console\PruneCommand;
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
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->booted(function () {
                $this->app->make(Schedule::class)->command(PruneCommand::class)->daily();
            });
        }

        $this->app->bind(CreatesResponse::class, FractalResponse::class);
    }
}
