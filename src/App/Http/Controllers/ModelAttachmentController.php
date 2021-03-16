<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Controllers;

use Asseco\Attachments\App\Traits\Attachable;
use Asseco\Attachments\App\Http\Requests\ModelAttachmentRequest;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class ModelAttachmentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param ModelAttachmentRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function store(ModelAttachmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $model = $this->getModel($validated);

        $model->attachments()->sync(Arr::get($validated, 'attachment_ids'));

        return response()->json($model);
    }

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    protected function getModel(array $data): Model
    {
        $modelNamespace = Arr::get($data, 'model');
        $modelId = Arr::get($data, 'model_id');

        if (!$modelNamespace instanceof Model) {
            throw new Exception("$modelNamespace is not a valid model namespace");
        }

        $model = $modelNamespace::query()->where('id', $modelId)->firstOrFail();

        if (!method_exists($model, 'attachments')) {
            throw new Exception("$modelNamespace doesn't have a attachments() method. Did you forget to use a " . Attachable::class . ' trait?');
        }

        return $model;
    }
}
