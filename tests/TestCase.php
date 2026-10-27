<?php

declare(strict_types=1);

namespace Asseco\Attachments\Tests;

use Asseco\Attachments\AttachmentsServiceProvider;
use Asseco\Common\CommonServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->runLaravelMigrations();
    }

    protected function getPackageProviders($app): array
    {
        // As in a service: laravel-common's provider is always loaded with this package.
        return [CommonServiceProvider::class, AttachmentsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
