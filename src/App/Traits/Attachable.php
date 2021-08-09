<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Attachable
{
    public function attachments(): MorphToMany
    {
        $model = config('asseco-attachments.models.attachment');

        return $this->morphToMany($model, 'attachable')->withTimestamps();
    }
}
