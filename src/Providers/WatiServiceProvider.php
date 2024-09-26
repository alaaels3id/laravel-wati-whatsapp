<?php

namespace Alaaelsaid\LaravelWatiWhatsapp\Providers;

use Alaaelsaid\LaravelWatiWhatsapp\Facade\WatiService;
use Illuminate\Support\ServiceProvider;

class WatiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/wati.php' => config_path('wati.php'),
        ],'wati');
    }

    public function register(): void
    {
        $this->app->singleton('Wati', fn() => new WatiService());

        $this->mergeConfigFrom(__DIR__ . '/../../config/wati.php','wati');
    }
}