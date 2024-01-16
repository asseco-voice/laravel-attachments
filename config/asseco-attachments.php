<?php

use Asseco\Attachments\App\Models\Attachment;
use Asseco\Attachments\App\Models\FilingPurpose;
use Asseco\BlueprintAudit\App\MigrationMethodPicker;

return [

    /**
     * Model bindings.
     */
    'models' => [
        'attachment' => Attachment::class,
        'filingPurpose' => FilingPurpose::class,
    ],

    'migrations' => [

        /**
         * UUIDs as primary keys.
         */
        'uuid'       => false,

        /**
         * Timestamp types.
         *
         * @see https://github.com/asseco-voice/laravel-common/blob/master/config/asseco-common.php
         */
        'timestamps' => MigrationMethodPicker::PLAIN,

        /**
         * Should the package run the migrations. Set to false if you're publishing
         * and changing default migrations.
         */
        'run'        => true,
    ],

    'routes' => [
        'prefix'     => 'api',
        'middleware' => ['api'],
    ],
];
