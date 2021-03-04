<?php

use Asseco\Attachments\Models\Attachment;
use Asseco\Attachments\App\Traits\Attachable;

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
     * Namespace to Attachable trait.
     */
    'trait_path'  => Attachable::class,


];
