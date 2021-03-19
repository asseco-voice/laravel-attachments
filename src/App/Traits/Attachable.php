<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Attachable
{
    public function attachments(): MorphToMany
    {
        return $this->morphToMany(Attachment::class, 'attachable')->withTimestamps();
    }
}
