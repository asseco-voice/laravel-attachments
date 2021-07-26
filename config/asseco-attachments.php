<?php


use Asseco\Attachments\App\Models\Attachment;

return [
    /**
     * Model which will be bound to the app.
     */
    'attachment_model' => Attachment::class,

    /**
     * Should the package run the migrations. Set to false if you're publishing
     * and changing default migrations.
     */
    'runs_migrations'  => true,
];
