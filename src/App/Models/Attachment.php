<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Asseco\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;

class Attachment extends Model implements \Asseco\Attachments\App\Contracts\Attachment
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected static function newFactory()
    {
        return AttachmentFactory::new();
    }

    public function attachables(): HasMany
    {
        return $this->hasMany(Attachable::class);
    }

    public static function createFrom(UploadedFile $file)
    {
        $fileHash = sha1_file($file->path());

        $path = $file->storeAs('attachments', date('U') . '_' . $file->getClientOriginalName());

        $data = [
            'name'      => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size'      => $file->getSize(),
            'path'      => $path,
            'hash'      => $fileHash,
        ];

        return self::query()->create($data);
    }
}
