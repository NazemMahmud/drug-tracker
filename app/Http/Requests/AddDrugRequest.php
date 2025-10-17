<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\RequestValidationErrorFormat;

class AddDrugRequest extends RequestValidationErrorFormat
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'rxcui' => 'required|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'rxcui.required' => 'The drug rxcui field is required.',
            'rxcui.string'   => 'The drug rxcui value must be a valid string.',
        ];
    }
}
