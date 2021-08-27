<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Traits;

use Asseco\Attachments\App\Contracts\Attachment;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Attachable
{
    public function attachments(): MorphToMany
    {
        return $this->morphToMany(get_class(app(Attachment::class)), 'attachable')
            ->withTimestamps()
            ->withPivot('id');
    }
}
