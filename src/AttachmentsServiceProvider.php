<?php

declare(strict_types=1);

namespace Asseco\Attachments;

use Illuminate\Support\ServiceProvider;

class AttachmentsServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/asseco-attachments.php', 'asseco-attachments');
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        if (config('asseco-attachments.runs_migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        }
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../migrations' => database_path('migrations'),
        ], 'asseco-attachments');

        $this->publishes([
            __DIR__ . '/../config/asseco-attachments.php' => config_path('asseco-attachments.php'),
        ], 'asseco-attachments');
    }
}
