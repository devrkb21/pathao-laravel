<?php

namespace devrkb21\PathaoLaravel;

use devrkb21\PathaoLaravel\Commands\PathaoSetupCommand;
use devrkb21\PathaoLaravel\Http\Controllers\PathaoWebhookController;
use Illuminate\Support\ServiceProvider;

class PathaoLaravelServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/pathao.php',
            'pathao'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // Config
        $this->publishes([
            __DIR__.'/../config/pathao.php' => config_path('pathao.php'),
        ], 'pathao-config');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register Webhook Route
        $this->app['router']->post('api/pathao/webhook', [PathaoWebhookController::class, 'handle'])
            ->middleware('api');

        if ($this->app->runningInConsole()) {
            $this->commands([
                PathaoSetupCommand::class,
                Commands\PathaoSyncLocationsCommand::class,
                Commands\PathaoClearCacheCommand::class,
                Commands\PathaoStatusCommand::class,
                Commands\PathaoMerchantInfoCommand::class,
            ]);
        }
    }
}
