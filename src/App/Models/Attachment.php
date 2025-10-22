<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Models;

use Asseco\Attachments\Database\Factories\AttachmentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

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

    public static function createFrom(UploadedFile $file, $filingPurposeId = null, ?string $originalName = null)
    {
        $fileHash = sha1_file($file->path());

        $basePath = 'attachments';
        if (config('asseco-attachments.path_group_by_ymd')) {
            $basePath .= '/' . date('Y/m/d');
        }

        $name = $originalName ?: $file->getClientOriginalName();

        // clean & sanitize
        $name = Str::ascii(Str::of($name)->squish());
        $name = preg_replace('/[^A-Za-z0-9._\-\pL ]/u', '_', $name);
        $name = substr($name, 0, 255);

        if ($originalName) {
            // potential fix extension
            $name = pathinfo($name, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $name = "{$name}.{$extension}";
        }

        $path = $file->storeAs($basePath, date('U') . '_' . $name);

        $data = [
            'name'      => $name,
            'mime_type' => $file->getClientMimeType(),
            'size'      => $file->getSize(),
            'path'      => $path,
            'hash'      => $fileHash,
        ];

        if ($filingPurposeId) {
            $data['filing_purpose_id'] = $filingPurposeId;
        }

        return self::query()->create($data);
    }
}
