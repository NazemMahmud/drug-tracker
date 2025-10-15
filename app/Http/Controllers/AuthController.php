<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\DTO\Auth\RegistrationDto;
use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Http\Requests\RegistrationRequest;
use App\Services\AuthService;

use Illuminate\Http\JsonResponse;

use Exception;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {}


    public function registration(RegistrationRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $data          = RegistrationDto::fromArray($validatedData);

            if ($this->authService->isUserExistsByEmail($data->email)) {
                return HttpHandler::errorResponse(Constants::USER_EXISTS, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data = $this->authService->register($data);

            if ($data) {
                return HttpHandler::successMessage(Constants::SUCCESS_REGISTER, JsonResponse::HTTP_CREATED);
            }

            return HttpHandler::errorResponse(Constants::SOMETHING_WENT_WRONG);

        } catch (Exception $ex) {
            return HttpHandler::errorResponse(Constants::ERROR_REGISTER);
        }
    }
}
