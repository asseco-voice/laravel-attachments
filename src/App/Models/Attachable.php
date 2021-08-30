<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Attachable extends MorphPivot
{
    protected $table = 'attachables';

    public function attachment(): BelongsTo
    {
        return $this->belongsTo(get_class(app(\Asseco\Attachments\App\Contracts\Attachment::class)));
    }

    public function related()
    {
        return $this->morphTo(__FUNCTION__, 'attachable_type', 'attachable_id');
    }
}
