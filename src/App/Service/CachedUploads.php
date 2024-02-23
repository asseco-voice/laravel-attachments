<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CachedUploads
{
    const DEF_CACHE_TIME = 3600;
    const DEF_CACHE_KEY = 'ASEE_ATTACHMENTS_MAP';
    const DEF_CACHE_PREFIX = 'ASEE_ATTACHMENT_';

    const STORAGE_TYPE_FILE = 'FILE';

    /**
     * Is attachments cache used or not.
     *
     * @return bool
     */
    public static function cacheUsed(): bool
    {
        return config('asseco-attachments.cache_upload.enabled') ?? false;
    }

    /**
     * Get storage type - FILE | CACHE.
     *
     * @return string
     */
    public static function getStorageType()
    {
        return config('asseco-attachments.cache_upload.type', self::STORAGE_TYPE_FILE) ?: self::STORAGE_TYPE_FILE;
    }

    /**
     * If type = FILE, this is where mapping is saved.
     *
     * @return string
     */
    public static function getCacheMapKey()
    {
        return config('asseco-attachments.cache_upload.cache_map_key', self::DEF_CACHE_KEY) ?: self::DEF_CACHE_KEY;
    }

    /**
     * If type = CACHE, this is prefix for acche key, one per attachment.
     *
     * @return string
     */
    public static function getCacheKeyPrefix()
    {
        return config('asseco-attachments.cache_upload.cache_key_prefix', self::DEF_CACHE_PREFIX) ?: self::DEF_CACHE_PREFIX;
    }

    /**
     * If type = FILE, this is where files are saved.
     *
     * @return string
     */
    public static function getFilesLocation()
    {
        $loc = config('asseco-attachments.cache_upload.file_location', sys_get_temp_dir()) ?: sys_get_temp_dir();

        return rtrim($loc, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public static function getCacheTime()
    {
        return config('asseco-attachments.cache_upload.cache_time', self::DEF_CACHE_TIME) ?: self::DEF_CACHE_TIME;
    }

    /**
     * Store uploaded files in tmp directory & save to Cache, for later quicker access.
     *
     * @param  UploadedFile  $file
     * @param  Model  $attachment
     * @return bool
     */
    public static function store(UploadedFile $file, Model $attachment)
    {
        // keep & cache
        if (!self::cacheUsed()) {
            return false;
        }

        $success = false;

        /** @var \Illuminate\Http\UploadedFile $file */
        $from = $file->getPathname();

        if (strtoupper(self::getStorageType()) == self::STORAGE_TYPE_FILE) {
            // stored in files
            $cacheKey = self::getCacheMapKey();
            $to = self::getFilesLocation() . $attachment->id;
            if (@move_uploaded_file($from, $to)) {
                $cacheData = [];
                if (Cache::has($cacheKey)) {
                    $cacheData = json_decode(Cache::get($cacheKey), true);
                }
                $cacheData[$attachment->id] = $to;
                Cache::put($cacheKey, json_encode($cacheData), self::getCacheTime());
                $success = true;
            }
        } else {
            // stored in cache
            $cacheKey = self::getCacheKeyPrefix() . $attachment->id;
            Cache::put($cacheKey, file_get_contents($from), self::getCacheTime());
            $success = true;
        }

        if ($success) {
            Log::debug('Attachment ' . $attachment->id . ' stored in cache: ' . ($to ?? $cacheKey), ['method' => __METHOD__]);
        }

        return $success;
    }

    /**
     * Get filename from cached attachment (stored in tmp folder).
     *
     * @param  Model  $attachment
     * @return string|null
     */
    public static function get(Model $attachment)
    {
        if (!self::cacheUsed()) {
            return null;
        }

        if (strtoupper(self::getStorageType()) == self::STORAGE_TYPE_FILE) {
            // stored in files
            $cacheKey = self::getCacheMapKey();
            if (!Cache::has($cacheKey)) {
                return null;
            }

            $cacheData = json_decode(Cache::get($cacheKey), true);
            $filename = Arr::get($cacheData, $attachment->id, null);
            if (empty($filename)) {
                return null;
            }

            if (is_file($filename)) {
                return $filename;
            }
        } else {
            // stored in cache
            $cacheKey = self::getCacheKeyPrefix() . $attachment->id;

            if (!Cache::has($cacheKey)) {
                return null;
            }

            try {
                $content = Cache::get($cacheKey);
                if (empty($content)) {
                    return null;
                }

                $filename = tempnam(self::getFilesLocation(), 'attach_');
                //$filename = self::getFilesLocation() . basename($attachment->path);
                file_put_contents($filename, $content);

                return $filename;
            } catch (\Exception $e) {
                Log::warning('Failed to restore attachment ' . $attachment->id . ' from cache to tmp dir.',
                    ['method' => __METHOD__]);
            }
        }

        return null;
    }
}
