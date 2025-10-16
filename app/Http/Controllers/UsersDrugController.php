<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Http\Requests\AddDrugRequest;
use App\Resources\UserDrugResource;
use App\Services\UsersDrugService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Exception;

class UsersDrugController extends Controller
{
    /**
     * all endpoints below are authenticated
     * - **Endpoints**:
     * - 1. **Add Drug**: Add a new drug to the user's medication list.
     * - Payload: **`rxcui`** (string)
     * - Validation: Ensure **`rxcui`** is valid (using National Library of Medicine API).
     * - **Delete Drug**:
     * - Description: Delete a drug from the user's medication list.
     * - Validation: Ensure **`rxcui`** is valid and exists in the user’s list.
     * - **Get User Drugs**:
     * - Description: Retrieve all drugs from the user's medication list.
     * - Returns: Rx ID, Drug name, baseNames (ingredientAndStrength), doseFormGroupName (doseFormGroupConcept).
     */

    public function __construct(
        protected UsersDrugService $usersDrugService,
    )
    {}

    public function store(AddDrugRequest $request): JsonResponse
    {
        try {
            $user      = $request->user();
            $validated = $request->validated();

            if ($this->usersDrugService->isUserDrugExist($user->id, $validated['rxcui'])) {
                return HttpHandler::successMessage(Constants::USER_DRUG_EXIST);
            }

            $result   = $this->usersDrugService->addDrug($user->id, $validated['rxcui']);
            $resource = new UserDrugResource($result);
            return HttpHandler::successResponse($resource, JsonResponse::HTTP_CREATED);
        } catch (Exception $ex) {
            dd($ex->getMessage());
            $statusCode = is_string($ex->getCode()) ? JsonResponse::HTTP_INTERNAL_SERVER_ERROR : $ex->getCode();
            dd($statusCode);
            HttpHandler::errorLogMessageHandler($ex->getMessage(), $statusCode);

            return HttpHandler::errorResponse(Constants::ERROR_DB_CREATE, $statusCode);
        }
    }

    /**
     * Note: requirement: Validation: Ensure rxcui is valid and exists in the user’s list.
     * If drug is not under user's medication, then nothing to delete whether it is a valid/invalid rxcui
     */
    public function destroy(Request $request, string $rxcui): JsonResponse
    {
        try {
            $user    = $request->user();
            $deleted = $this->usersDrugService->deleteDrug($user->id, $rxcui);

            if (!$deleted) {
                return HttpHandler::errorResponse(Constants::USER_DRUG_NOT_FOUND, JsonResponse::HTTP_NOT_FOUND);
            }

            return HttpHandler::successMessage(Constants::USER_DRUG_DELETED);

        } catch (Exception $ex) {
            $statusCode = $ex->getCode() ?: JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());

            return HttpHandler::errorResponse(Constants::ERROR_DB_DELETE, $statusCode);
        }
    }


    public function index(Request $request): JsonResponse
    {
        // Rx ID, Drug name, baseNames (ingredientAndStrength), doseFormGroupName (doseFormGroupConcept).
        try {
            $user = $request->user();
            $drugs = $this->usersDrugService->getUserDrugs($user->id);

            return HttpHandler::successResponse($drugs);
        } catch (Exception $ex) {
            $statusCode = $ex->getCode() ?: JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            HttpHandler::errorLogMessageHandler($ex->getMessage(), $statusCode);

            return HttpHandler::errorResponse(Constants::ERROR_DB_READ, $statusCode);
        }
    }
}
