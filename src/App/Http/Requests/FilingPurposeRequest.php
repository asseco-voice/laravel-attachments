<?php

declare(strict_types=1);

namespace Asseco\Attachments\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilingPurposeRequest extends FormRequest
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
            'name' => 'required|string',
            'label' => 'required|string',
            'module' => 'required|string',
            'default' => 'required|boolean',
        ];
    }
}
