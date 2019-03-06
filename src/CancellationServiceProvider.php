<?php

namespace Signifly\Cancellation;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class CancellationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/cancellation.php' => config_path('cancellation.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/cancellation.php', 'cancellation');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Blueprint::macro('cancellable', function () {
            return $this->timestamp('cancelled_at')->nullable();
        });
    }
}
