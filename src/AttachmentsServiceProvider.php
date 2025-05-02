<?php

declare(strict_types=1);

namespace Asseco\Attachments;

use Asseco\Attachments\App\Contracts\Attachment;
use Asseco\Attachments\App\Contracts\FilingPurpose;
use Illuminate\Support\Facades\Route;
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

        if (config('asseco-attachments.migrations.run')) {
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

        $this->app->bind(Attachment::class, config('asseco-attachments.models.attachment'));

        Route::model('attachment', get_class(app(Attachment::class)));

        $this->app->bind(FilingPurpose::class, config('asseco-attachments.models.filing_purpose'));

        Route::model('filing_purpose', get_class(app(FilingPurpose::class)));
    }
}
