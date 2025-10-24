<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'original_name'     => 'required|string|max:250',
            'mime_type'         => 'required|string|max:50',
            'size'              => 'nullable|int|min:0',
            'path'              => 'nullable|string|max:255',
            'filing_purpose_id' => 'sometimes|string|exists:filing_purposes,id',
            'external_id'       => 'nullable|string|max:255',
        ];
    }
}
