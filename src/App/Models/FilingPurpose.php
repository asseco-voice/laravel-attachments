<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilingPurpose extends Model implements \Asseco\Attachments\App\Contracts\FilingPurpose
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
