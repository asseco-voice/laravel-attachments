<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Controllers;

use Asseco\Attachments\App\Contracts\Attachment as AttachmentContract;
use Asseco\Attachments\App\Http\Requests\AttachmentRequest;
use Asseco\Attachments\App\Models\Attachment;
use Exception;
use Illuminate\Http\JsonResponse;
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
        $file = $request->file('attachment');

        $attachment = $this->attachment::createFrom($file);

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
}
