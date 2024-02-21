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
        'uuid' => false,

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
        'run' => true,
    ],

    'routes' => [
        'prefix' => 'api',
        'middleware' => ['api'],
    ],

    // Cached attachments
    'cache_upload' => [
        'enabled'           => env('ATTACHMENTS_CACHE_ENABLED', false),

        // where are the files stored: in filesystem (FILE) or Cache (CACHE)
        'type'              => env('ATTACHMENTS_CACHE_TYPE', 'FILE'),

        // if type = FILE, files are stored in this path
        'file_location'     => env('ATTACHMENTS_CACHE_LOCATION', '/tmp/'),

        // Cache key where mapping is saved [attachment_id => filename, attachment_id => filename, ...]
        'cache_map_key'     => env('ATTACHMENTS_CACHE_MAP_KEY', 'ASEE_ATTACHMENTS_MAP'),

        // Cache key prefix when Cache type is used - attachments are stored separately
        // key is determined as: prefix + attachment_id
        'cache_key_prefix'  => env('ATTACHMENTS_CACHE_KEY_PREFIX', 'ASEE_ATTACHMENT_'),

        // how long is cache valid, in seconds
        'cache_time'        => env('ATTACHMENTS_CACHE_TIME', 3600),
    ],
];
