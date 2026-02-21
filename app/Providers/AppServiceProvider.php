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
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Services\AppSettingsService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AppSettingsService::class);
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
        $this->registerViewComposers();
        $this->applyMailConfig();
    }

    /**
     * Override mail config from DB settings (if configured).
     * Falls back to .env values if no SMTP host is saved in DB.
     */
    protected function applyMailConfig(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
                return;
            }

            $settings = \App\Models\AppSetting::first();

            if (!$settings || empty($settings->smtp_host)) {
                return;
            }

            config([
                'mail.mailers.smtp.host'       => $settings->smtp_host,
                'mail.mailers.smtp.port'       => $settings->smtp_port ?? 587,
                'mail.mailers.smtp.encryption' => $settings->smtp_encryption ?: null,
                'mail.mailers.smtp.username'   => $settings->smtp_username,
                'mail.mailers.smtp.password'   => $settings->smtp_password,
                'mail.from.address'            => $settings->smtp_from_address ?: config('mail.from.address'),
                'mail.from.name'               => $settings->smtp_from_name ?: config('mail.from.name'),
            ]);
        } catch (\Throwable) {
            // Silently skip if DB unavailable (e.g. during fresh migrations)
        }
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

    protected function registerViewComposers(): void
    {
        View::composer(['layouts.app', 'layouts.guest'], function ($view) {
            $view->with('appSettings', app(AppSettingsService::class)->get());
        });
    }
}
