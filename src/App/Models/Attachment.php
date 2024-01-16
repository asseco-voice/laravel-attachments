<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Asseco\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function filingPurpose(): BelongsTo
    {
        return $this->belongsTo(FilingPurpose::class);
    }

    public static function createFrom(UploadedFile $file, $filingPurposeId = null)
    {
        $fileHash = sha1_file($file->path());

        $path = $file->storeAs('attachments', date('U') . '_' . $file->getClientOriginalName());

        $data = [
            'name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => $path,
            'hash' => $fileHash,
        ];

        if ($filingPurposeId) {
            $data['filing_purpose_id'] = $filingPurposeId;
        }

        return self::query()->create($data);
    }
}
