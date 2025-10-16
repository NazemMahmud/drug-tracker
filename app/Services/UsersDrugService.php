<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Repositories\UsersDrugRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

use Exception;

class UsersDrugService
{
    public function __construct(
        protected UsersDrugRepository $usersDrugRepository,
        protected DrugService $drugService
    )
    {}

    public function addDrug(int $userId, string $rxcui): ?Model
    {
        $isValidRxcui = DrugService::isValidRxcui($rxcui);
        if (!$isValidRxcui) {
            throw new Exception("Invalid rxcui", JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $newData = $this->usersDrugRepository->create([
                'user_id' => $userId,
                'rxcui'   => $rxcui
            ]);
        } catch (Exception $ex) {
            $errorData = [
                'user_id' => $userId,
                'rxcui'   => $rxcui,
                'trace'   => $ex->getTraceAsString()
            ];
            HttpHandler::errorHandler('Error adding drug: ' . $ex->getMessage(), $errorData);

            throw new Exception(Constants::ERROR_DB_CREATE, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $newData;
    }

    public function isUserDrugExist(int $userId, string $rxcui): bool
    {
        return $this->usersDrugRepository->existsBy([
            'user_id' => $userId,
            'rxcui'   => $rxcui
        ]);
    }

    public function deleteDrug(int $userId, string $rxcui): bool
    {
        try {
            return $this->usersDrugRepository->deleteBy([
                'user_id' => $userId,
                'rxcui' => $rxcui
            ]);
        } catch (Exception $ex) {
            $errorData = [
                'user_id' => $userId,
                'rxcui'   => $rxcui,
                'trace'   => $ex->getTraceAsString()
            ];
            HttpHandler::errorHandler('Error deleting drug: ' . $ex->getMessage(), $errorData);
            throw new Exception(Constants::ERROR_DB_DELETE, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * todo: not completed
     */
    public function getUserDrugs(int $userId): array
    {
        try {
            $userDrugs = $this->usersDrugRepository->getAllBy(['user_id' => $userId]);

            if ($userDrugs->isEmpty()) {
                return [];
            }

            $drugsWithDetails = [];
//            $data = [
//                ['rxcui'] => '',
//                ['rxcui'] => '',
//                ['rxcui'] => '',
//                ['rxcui'] => '',
//            ];

            foreach ($userDrugs as $userDrug) {
                // Fetch drug details from National Library of Medicine API
                $drugDetails = $this->drugService->getDrugDetails($userDrug->rxcui);

                if ($drugDetails) {
                    $drugsWithDetails[] = [
                        'id' => $userDrug->id,
                        'rxcui' => $userDrug->rxcui,
                        'drug_name' => $drugDetails['name'] ?? null,
                        'base_names' => $drugDetails['ingredientAndStrength'] ?? null,
                        'dose_form_group_name' => $drugDetails['doseFormGroupConcept'] ?? null,
                        'added_at' => $userDrug->created_at,
                    ];
                }
            }

            return $drugsWithDetails;
        } catch (Exception $ex) {
            Log::error('Error fetching user drugs: ' . $ex->getMessage(), [
                'user_id' => $userId,
                'trace' => $ex->getTraceAsString()
            ]);
            throw new Exception(Constants::ERROR_DB_READ, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
