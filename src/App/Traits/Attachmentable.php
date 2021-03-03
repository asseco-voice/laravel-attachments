<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Asseco\Attachments\App\Models\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Attachmentable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'model');
    }
}
