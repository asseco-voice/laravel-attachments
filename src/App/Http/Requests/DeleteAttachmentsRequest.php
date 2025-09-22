<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAttachmentsRequest extends FormRequest
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
            'with_attachable'   => 'boolean|nullable',
            'attachment_ids'    => 'required|array',
            'attachment_ids.*'  => 'exists:attachments,id',
        ];
    }
}
