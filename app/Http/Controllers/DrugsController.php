<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Helpers\Constants;
use App\Helpers\HttpHandler;
use App\Services\DrugService;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Exception;

class DrugsController extends Controller
{
    private int $limit = 5;

    public function __construct(
        protected DrugService $drugService
    )
    {}

    public function searchDrugs(Request $request): JsonResponse
    {
        try {
            $drugName = strtolower($request->query('drug_name') ?? '');
            if (empty($drugName)) {
                return HttpHandler::errorResponse(Constants::ERROR_DRUG_NAME_REQUIRED, JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $details = $this->drugService->searchDrugsDetails($drugName, $this->limit);
            return HttpHandler::successResponse($details);
        } catch (Exception $ex) {
            Log::error('Controller error: ' . $ex->getMessage());
            return HttpHandler::errorResponse($ex->getMessage(),JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
