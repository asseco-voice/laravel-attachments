<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Controllers;

use Asseco\Attachments\App\Contracts\Attachment as AttachmentContract;
use Asseco\Attachments\App\Http\Requests\AttachmentRequest;
use Asseco\Attachments\App\Http\Requests\DeleteAttachmentsRequest;
use Asseco\Attachments\App\Http\Requests\AttachmentUpdateRequest;
use Asseco\Attachments\App\Models\Attachment;
use Asseco\Attachments\App\Service\CachedUploads;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * @param Attachment $attachment
     * @param AttachmentUpdateRequest $request
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
        return Storage::download($attachment->path, $attachment->name);
    }

    /**
     * Remove multiple resources from storage.
     *
     * @param DeleteAttachmentsRequest $request
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
                    'exception' => $e,
                ]);
            }
        }
        return response()->json([
            'message' => 'Bulk delete operation completed',
            'deleted' => $deleted,
            'not_deleted' => $notDeleted,
        ]);
    }
}
