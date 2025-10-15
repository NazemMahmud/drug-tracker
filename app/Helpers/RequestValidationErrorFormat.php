<?php

declare(strict_types=1);

namespace App\Helpers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RequestValidationErrorFormat extends FormRequest
{
    public function withValidator(Validator $validator): void
    {
        if ($validator->fails()) {
            $errors = $validator->errors();
            $error  = [];

            foreach ($errors->messages() as $messages) {
                $error[] = $messages;
            }

            $errors = array_reduce($error, 'array_merge', array());
            throw new HttpResponseException(
                HttpHandler::errorResponse($errors[0], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
    }
}
