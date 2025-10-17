<?php

namespace App\Services;

use App\DTO\Auth\RegistrationDto;
use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Repositories\AuthRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

use Exception;

class AuthService
{
    public function __construct(protected AuthRepository $authRepository)
    {}

    public function register(RegistrationDto $data): ?Model
    {
        try {
            $data->password = Hash::make($data->password);
            $response       = $this->authRepository->create($data->toArray());
            return $response;
        } catch (Exception $ex) {
            HttpHandler::errorLogMessageHandler(Constants::ERROR_REGISTER. " : " . $ex->getMessage(), $ex->getCode());
            throw new Exception(Constants::ERROR_REGISTER, JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function isUserExistsByEmail(string $email): bool
    {
        try {
            return $this->authRepository->existsBy(['email' => $email]);
        } catch (Exception $ex) {
            HttpHandler::errorLogMessageHandler(Constants::ERROR_DB. " : " . $ex->getMessage(), $ex->getCode());
            throw new Exception(Constants::ERROR_DB, JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
