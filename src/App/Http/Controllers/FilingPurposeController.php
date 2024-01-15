<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Controllers;

use Asseco\Attachments\App\Contracts\FilingPurpose as FilingPurposeContract;
use Asseco\Attachments\App\Http\Requests\FilingPurposeIndexRequest;
use Asseco\Attachments\App\Http\Requests\FilingPurposeRequest;
use Asseco\Attachments\App\Models\FilingPurpose;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FilingPurposeController extends Controller
{
    public FilingPurposeContract $filingPurpose;

    public function __construct(FilingPurposeContract $filingPurpose)
    {
        $this->filingPurpose = $filingPurpose;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  FilingPurposeIndexRequest  $request
     * @return JsonResponse
     */
    public function index(FilingPurposeIndexRequest $request): JsonResponse
    {
        $filingPurposes = $this->filingPurpose::where('module', $request->validated())->get();

        return response()->json($filingPurposes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FilingPurposeRequest  $request
     * @return JsonResponse
     */
    public function store(FilingPurposeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['default_purpose']) {
            $this->filingPurpose::query()
                ->where('default_purpose', true)
                ->update(['default_purpose' => false]);
        }
        $filingPurpose = $this->filingPurpose::query()->create($validated);

        return response()->json($filingPurpose);
    }

    /**
     * Display the specified resource.
     *
     * @param  FilingPurpose  $filingPurpose
     * @return JsonResponse
     */
    public function show(FilingPurpose $filingPurpose): JsonResponse
    {
        return response()->json($filingPurpose);
    }

    /**
     * Display the specified resource.
     *
     * @param  FilingPurpose  $filingPurpose
     * @param  FilingPurposeRequest  $request
     * @return JsonResponse
     */
    public function update(FilingPurpose $filingPurpose, FilingPurposeRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($validated['default_purpose']) {
            $this->filingPurpose::query()
                ->where('default_purpose', true)
                ->update(['default_purpose' => false]);
        }
        $filingPurpose->update($validated);

        return response()->json($filingPurpose->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  FilingPurpose  $filingPurpose
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(FilingPurpose $filingPurpose): JsonResponse
    {
        $isDeleted = $filingPurpose->delete();

        return response()->json($isDeleted ? 'true' : 'false');
    }
}
