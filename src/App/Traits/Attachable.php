<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attachable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->withTimestamps();
    }
}
