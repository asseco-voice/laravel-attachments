<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentUpdateRequest extends FormRequest
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
            'filing_purpose_id' => 'required|string|exists:filing_purposes,id',
            'external_id'       => 'nullable|string|max:255',
        ];
    }
}
