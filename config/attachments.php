<?php

use Asseco\Attachments\Models\Attachment;
use Asseco\Attachments\App\Traits\Attachmentable;

return [
    /**
     * Path to Laravel models in 'path => namespace' format.
     *
     * This does not recurse in folders, so you need to specify
     * an array of paths if non-standard models are to be used
     */
    'models_path' => [
        app_path('Models') => 'App\\Models\\',
    ],

    /**
     * Namespace to Attachmentable trait.
     */
    'trait_path'  => Attachmentable::class,


];
