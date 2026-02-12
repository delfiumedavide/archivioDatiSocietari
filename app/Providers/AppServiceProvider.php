<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Document;
use App\Observers\CompanyObserver;
use App\Observers\DocumentObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        $this->configureRateLimiting();
        $this->registerObservers();
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $maxAttempts = config('archivio.rate_limit.login_attempts', 5);
            $decayMinutes = config('archivio.rate_limit.login_decay_minutes', 1);

            $key = $request->ip() . '|' . $request->input('email', '');

            return Limit::perMinutes($decayMinutes, $maxAttempts)->by($key);
        });
    }

    /**
     * Register model observers.
     */
    protected function registerObservers(): void
    {
        Document::observe(DocumentObserver::class);
        Company::observe(CompanyObserver::class);
    }
}
