<?php

namespace Asseco\Attachments\Tests;

use Asseco\Attachments\AttachmentsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->runLaravelMigrations();
    }

    protected function getPackageProviders($app): array
    {
        return [AttachmentsServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
