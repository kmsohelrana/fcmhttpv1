<?php

namespace Kmsohelrana\Fcmhttpv1\Providers;

use Illuminate\Support\ServiceProvider;

class FcmServiceProvider extends ServiceProvider
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
        $this->publishes([
            __DIR__ . '/config/fcm_config.php' => config_path('fcm_config.php'),
        ], 'fcmhttpv1');
    }
}
