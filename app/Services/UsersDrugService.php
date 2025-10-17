<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Repositories\DrugRepository;
use App\Repositories\UsersDrugRepository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

use Exception;

class UsersDrugService
{
    public function __construct(
        protected UsersDrugRepository $usersDrugRepository,
        protected DrugService         $drugService,
        protected DrugRepository      $drugRepository
    )
    {
    }

    public function setDrugDetailsByRxcui(string $rxcui): void
    {
        $drugDetails = $this->drugService->fetchSingleDrugDetails($rxcui);
        if (empty($drugDetails)) {
            throw new Exception("Failed to fetch drug details", JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $conditions = ['rxcui' => $rxcui];
        $drugData   = [
            'name'                  => $drugDetails['name'] ?? '',
            'base_names'            => $drugDetails['base_names'] ?? [],
            'dose_form_group_names' => $drugDetails['dose_form_group_names'] ?? [],
        ];

        $drug = $this->drugRepository->firstOrCreateBy($conditions, $drugData);
    }

    public function addDrug(int $userId, string $rxcui): ?Model
    {
        DB::beginTransaction();
        try {
            $this->setDrugDetailsByRxcui($rxcui);
            $newData = $this->usersDrugRepository->create([
                'user_id' => $userId,
                'rxcui'   => $rxcui
            ]);

            $this->clearUserDrugsCache($userId);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();

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
            $deleted = $this->usersDrugRepository->deleteBy([
                'user_id' => $userId,
                'rxcui'   => $rxcui
            ]);

            if ($deleted) {
                $this->clearUserDrugsCache($userId);
            }

            return $deleted;
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

    public function getUserDrugs(int $userId): array
    {
        try {
            $cacheKey = $this->getUserDrugsCacheKey($userId);
            $ttl      = config('cache.ttl.drug_search', 3600);

            return Cache::remember($cacheKey, $ttl * 60, function () use ($userId) {
                $userDrugs = $this->usersDrugRepository->getUserDrugsWithDetails($userId);

                if ($userDrugs->isEmpty()) {
                    return [];
                }

                return $userDrugs->map(function ($userDrug) {
                    if (isset($userDrug->drug)) {
                        return [
                            'id'                    => $userDrug->id,
                            'rxcui'                 => $userDrug->rxcui,
                            'drug_name'             => $userDrug->drug->name,
                            'base_names'            => $userDrug->drug->base_names,
                            'dose_form_group_names' => $userDrug->drug->dose_form_group_names
                        ];
                    }
                })->toArray();
            });
        } catch (Exception $ex) {
            HttpHandler::errorHandler(
                'Error fetching user drugs: ' . $ex->getMessage(),
                [
                    'user_id' => $userId,
                    'trace'   => $ex->getTraceAsString()
                ]
            );
            throw new Exception(Constants::ERROR_DB_READ, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function getUserDrugsCacheKey(int $userId): string
    {
        return "user_drugs:{$userId}";
    }

    private function clearUserDrugsCache(int $userId): void
    {
        Cache::forget($this->getUserDrugsCacheKey($userId));
    }
}
