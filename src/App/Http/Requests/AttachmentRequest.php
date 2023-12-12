<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttachmentRequest extends FormRequest
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
            'attachment' => 'required|file',
            'filing_purpose_id' => 'sometimes|string|exists:filing_purposes,id',
        ];
    }
}
