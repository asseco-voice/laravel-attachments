<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Attachable
{
    public function attachments(): MorphToMany
    {
        $model = config('asseco-attachments.attachment_model');

        return $this->morphToMany($model, 'attachable')->withTimestamps();
    }
}
