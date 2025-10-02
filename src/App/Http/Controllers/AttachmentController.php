<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Controllers;

use Asseco\Attachments\App\Contracts\Attachment as AttachmentContract;
use Asseco\Attachments\App\Http\Requests\AttachmentRequest;
use Asseco\Attachments\App\Http\Requests\AttachmentUpdateRequest;
use Asseco\Attachments\App\Http\Requests\DeleteAttachmentsRequest;
use Asseco\Attachments\App\Http\Requests\DownloadAttachmentsRequest;
use Asseco\Attachments\App\Models\Attachment;
use Asseco\Attachments\App\Service\CachedUploads;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream\ZipStream;

class AttachmentController extends Controller
{
    public AttachmentContract $attachment;

    public function __construct(AttachmentContract $attachment)
    {
        $this->attachment = $attachment;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->attachment::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  AttachmentRequest  $request
     * @return JsonResponse
     */
    public function store(AttachmentRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = Arr::get($validated, 'attachment');
        $filingPurposeId = Arr::get($validated, 'filing_purpose_id');

        if ($impersonateUserId = $request->header('impersonate-user-id')) {
            // When request comes from background service, and we want to know who is responsible for attachment
            $user = auth()->user();
            if ($user) {
                $user->user_id = $impersonateUserId;
                if (method_exists($user, 'setIsServiceToken')) {
                    $user->setIsServiceToken(false);
                }
            }
        }

        $attachment = $this->attachment::createFrom($file, $filingPurposeId);

        CachedUploads::store($file, $attachment);

        return response()->json($attachment->refresh());
    }

    /**
     * Display the specified resource.
     *
     * @param  Attachment  $attachment
     * @return JsonResponse
     */
    public function show(Attachment $attachment): JsonResponse
    {
        return response()->json($attachment);
    }

    /**
     * Update the specified resource.
     *
     * @param  Attachment  $attachment
     * @param  AttachmentUpdateRequest  $request
     * @return JsonResponse
     */
    public function update(Attachment $attachment, AttachmentUpdateRequest $request): JsonResponse
    {
        $attachment->update($request->validated());

        return response()->json($attachment->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Attachment  $attachment
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        Storage::delete($attachment->path);
        $isDeleted = $attachment->delete();

        return response()->json($isDeleted ? 'true' : 'false');
    }

    public function download(Attachment $attachment)
    {
        if (Storage::exists($attachment->path)) {
            return Storage::download($attachment->path, $attachment->name);
        }
        else if (config('asseco-attachments.fallback_download.enabled')) {
            return $this->tryFallbackDownload($attachment);
        }

        throw new FileNotFoundException($attachment->path . ' not found in Storage!', 404);
    }

    /**
     * Remove multiple resources from storage.
     * Hard delete
     *
     * @param  DeleteAttachmentsRequest  $request
     * @return JsonResponse
     */
    public function bulkDelete(DeleteAttachmentsRequest $request): JsonResponse
    {
        $attachmentIds = $request->input('attachment_ids');
        $deleted = [];
        $notDeleted = [];
        $paths = Attachment::whereIn('id', $attachmentIds)->pluck('path', 'id');
        foreach ($attachmentIds as $id) {
            $path = $paths->get($id);
            try {
                Attachment::where('id', $id)->delete();
                if ($path) {
                    Storage::delete($path);
                }
                $deleted[] = $id;
            } catch (Exception $e) {
                $notDeleted[] = $id;
                Log::error('Failed to delete attachment', [
                    'attachment_id' => $id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Bulk delete operation completed',
            'deleted' => $deleted,
            'not_deleted' => $notDeleted,
        ]);
    }

    /**
     * @param DeleteAttachmentsRequest $request
     * @return JsonResponse
     */
    public function bulkSoftDelete(DeleteAttachmentsRequest $request): JsonResponse
    {
        $attachmentIds = $request->input('attachment_ids');
        $deleteAttachable = $request->boolean('with_attachable', true);

        $deleted = [];
        $notDeleted = [];
        $attachments = $this->attachment::whereIn('id', $attachmentIds)->get();
        foreach ($attachments as $attachment) {
            $path = $attachment->path;
            try {
                $attachment->delete();
                if ($path) {
                    Storage::delete($path);
                }

                if ($deleteAttachable) {
                    $attachment->attachables()->delete();
                }

                $deleted[] = $attachment->id;
            } catch (Exception $e) {
                $notDeleted[] = $attachment->id;
                Log::error('Failed to delete attachment', [
                    'attachment_id' => $attachment->id,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'message' => 'Bulk delete operation completed',
            'deleted' => $deleted,
            'not_deleted' => $notDeleted,
        ]);
    }

    public function bulkDownload(DownloadAttachmentsRequest $request): StreamedResponse
    {
        $attachmentIds = $request->input('attachment_ids');
        $attachments = Attachment::whereIn('id', $attachmentIds)->get();

        return response()->stream(function () use ($attachments) {
            $zip = new ZipStream(
                comment: 'Generated on ' . now()->toDateTimeString(),
                sendHttpHeaders: true,
                outputName: 'attachments_' . date('Y-m-d_H-i-s') . '.zip'
            );

            $successCount = 0;
            $failedFiles = [];

            foreach ($attachments as $attachment) {
                if(Storage::exists($attachment->path)) {
                    try {
                        $zip->addFileFromPath(
                            fileName: $this->generateUniqueFilename($attachment, $successCount),
                            path: Storage::path($attachment->path)
                        );
                        $successCount++;
                    } catch (Exception $e) {
                        $failedFiles[] = $attachment->name;
                        Log::warning('Failed to add file to ZIP', [
                            'attachment_id' => $attachment->id,
                            'filename' => $attachment->name,
                            'error' => $e->getMessage()
                        ]);
                    }
                } else {
                    $failedFiles[] = $attachment->name . ' (file not found)';
                }
            }

            if (!empty($failedFiles)) {
                $summary = "Download Summary\n================\n\n";
                $summary .= "Successfully downloaded: {$successCount} files\n";
                $summary .= "Failed files:\n" . implode("\n", $failedFiles);

                $zip->addFile('_download_summary.txt', $summary);
            }

            $zip->finish();
        }, 200, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="attachments_' . date('Y-m-d_H-i-s') . '.zip"',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function generateUniqueFilename(Attachment $attachment, int $index): string
    {
        $extension = pathinfo($attachment->name, PATHINFO_EXTENSION);
        $basename = pathinfo($attachment->name, PATHINFO_FILENAME);

        // Sanitize and ensure uniqueness
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);

        return $sanitized . '_' . $attachment->id . '.' . $extension;
    }


    /**
     * @param Attachment $attachment
     * @return StreamedResponse
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function tryFallbackDownload(Attachment $attachment) : StreamedResponse {
        $url = (config('asseco-attachments.fallback_download.url'));
        if (empty($url)) {
            throw new Exception('Fallback download URL is not set!');
        }

        // append attachment ID into URL
        $url = rtrim($url, '/') . '/' . $attachment->id;
        $response = Http::get(
            $url,
            [
                'service'   => strtolower(Str::snake(config('app.name', ''))),
                'path'      => $attachment->path,
            ]
        )->throw();

        $contentType = $response->header('Content-Type') ?: $attachment->mime_type;

        return response()->streamDownload(
            function () use ($response) {
                echo $response->body();
            },
            $attachment->name ?? 'download.bin',
            [
                'Content-Type' => $contentType,
            ]
        );
    }
}
