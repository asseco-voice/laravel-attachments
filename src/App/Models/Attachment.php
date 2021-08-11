<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Asseco\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Attachment extends Model implements \Asseco\Attachments\App\Contracts\Attachment
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }

    public function models(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'attachable')->withTimestamps();
    }
}
