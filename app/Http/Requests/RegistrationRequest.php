<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Helpers\RequestValidationErrorFormat;

class RegistrationRequest extends RequestValidationErrorFormat
{
    protected $stopOnFirstFailure = true;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => 'required|string',
            'email'    => 'required|string|email',
            'password' => 'required|string|min:6'
        ];
    }
}
