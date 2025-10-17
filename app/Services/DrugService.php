<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\HttpHandler;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

use Exception;

class DrugService
{
    protected static int $timeout = 1000;
    protected string $tty  = 'SBD';

    protected CONST GET_DRUGS_URL        = 'https://rxnav.nlm.nih.gov/REST/drugs.json?name=%s';
    protected const GET_DRUG_DETAILS_URL = "https://rxnav.nlm.nih.gov/REST/rxcui/%s/historystatus.json";


    /**
     * 1. Get drugs by drug_name
     * 2. Filter with tty = “SBD” from conceptGroup
     * 3. Fetch the “name” of the top 5 results using the history api
     * 4. Filter baseName ingredientAndStrength
     * 5. Also, different doseFormGroupName from doseFormGroupConcept
     * 6. Result will include: rxcui (ID), Drug name (string), Ingredient base names (array), Dosage form (array).
     *
     * @param string $drugName
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function searchDrugsDetails(string $drugName, int $limit): array
    {
        $cacheKey = "drug_search:{$drugName}";
        $ttl      = config('cache.ttl.drug_search', 3600);

        return Cache::remember($cacheKey, $ttl, function () use ($drugName, $limit) {
            try {
                $drugInfo = $this->getDrugs($drugName);
                $drugData = $this->extractDrugInfo($drugInfo, $limit);
                $details  = $this->getDrugDetails($drugData);

                return $details;
            } catch (Exception $ex) {
                HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());
                throw new Exception("Error fetching drug information for $drugName", $ex->getCode());
            }
        });
    }

    /**
     * Fetch drugs information for rxcui (rxnorm identifier) and name list
     *
     * @param string $drugName
     * @return array
     * @throws Exception
     */
    public function getDrugs(string $drugName): array
    {
        try {
            $url = sprintf(self::GET_DRUGS_URL, $drugName);
            $response = Http::timeout(self::$timeout)->get($url);

            if (!$response->successful()) {
                throw new Exception("API request failed with status: " . $response->status());
            }

            return $response->json();
        } catch (Exception $ex) {
            HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());
            throw new Exception("Error fetching drug information for $drugName", $ex->getCode());
        }
    }

    /**
     * Take n(default 5) items from the drugs search list for tty SBD
     *
     * @param array $drugInfo
     * @param int $limit
     * @return array
     */
    public function extractDrugInfo(array $drugInfo, int $limit): array
    {
        $conceptGroups = data_get($drugInfo, 'drugGroup.conceptGroup', []);
        if (empty($conceptGroups)) return [];

        foreach ($conceptGroups as $group) {
            if (($group['tty'] ?? null) === $this->tty && !empty($group['conceptProperties'])) {
                $properties = array_slice($group['conceptProperties'], 0, $limit);

                return array_map(fn($property) => [
                    'rxcui'     => $property['rxcui'],
                    'name'      => $property['name'],
                ], $properties);
            }
        }

        return [];
    }

    /**
     * from history API get base name and Dosage form
     */
    public function getDrugDetails(array $drugData): array
    {
        if (empty($drugData)) {
            return [];
        }

        $details = $this->fetchDrugDetailsPool($drugData);
        $result  = $this->filterDrugDetailsData($details, $drugData);

        return $result;
    }

    public function filterDrugDetailsData(array $responses, array $drugData): array
    {
        $result = [];

        foreach ($responses as $index => $response) {
            $drug = $drugData[$index];
            $rxcui = $drug['rxcui'];

            if ($response->successful()) {
                $details = $response->json();
                $result[] = [
                    'rxcui'                 => $rxcui,
                    'name'                  => $drug['name'] ?? data_get($details, 'rxcuiStatusHistory.attributes.name', ''),
                    'base_names'            => $this->extractBaseNames($details),
                    'dose_form_group_names' => $this->extractDoseFormGroups($details),
                ];
            } else {
                $errorMessage = "Failed to fetch drug details: ";
                $errorData = [
                    'rxcui'  => $rxcui,
                    'status' => $response->status(),
                ];
                HttpHandler::errorHandler($errorMessage, $errorData);
            }
        }

        return $result;
    }

    public function fetchSingleDrugDetails(string $rxcui): ?array
    {
        $cacheKey = "drug_details:{$rxcui}";
        $ttl = config('cache.ttl.drug_details', 86400);

        return Cache::remember($cacheKey, $ttl, function () use ($rxcui) {
            try {
                $url = sprintf(self::GET_DRUG_DETAILS_URL, $rxcui);
                $response = Http::timeout(self::$timeout)->get($url);

                if (!$response->successful()) {
                    throw new Exception("Failed to fetch drug details for rxcui: {$rxcui}");
                }

                $details = $response->json();

                return [
                    'rxcui'                 => $rxcui,
                    'name'                  => data_get($details, 'rxcuiStatusHistory.attributes.name', ''),
                    'base_names'            => $this->extractBaseNames($details),
                    'dose_form_group_names' => $this->extractDoseFormGroups($details),
                ];
            } catch (Exception $ex) {
                HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());
                throw $ex;
            }
        });
    }

    public function fetchDrugDetailsPool(array $rxcuiList): array
    {
        if (empty($rxcuiList)) {
            return [];
        }

        $responses = Http::pool(function ($pool) use ($rxcuiList) {
            $promises = [];
            foreach ($rxcuiList as $index => $drug) {
                $url = sprintf(self::GET_DRUG_DETAILS_URL, $drug['rxcui'] ?? '');
                $promises[$index] = $pool->get($url);
            }
            return $promises;
        });

        return $responses;
    }

    private function extractBaseNames(array $details): array
    {
        $ingredients = data_get(
            $details,
            'rxcuiStatusHistory.definitionalFeatures.ingredientAndStrength',
            []
        );

        return array_values(
            array_filter(
                array_column($ingredients, 'baseName')
            )
        );
    }

    private function extractDoseFormGroups(array $details): array
    {
        $doseFormGroups = data_get(
            $details,
            'rxcuiStatusHistory.definitionalFeatures.doseFormGroupConcept',
            []
        );

        return array_values(
            array_unique(
                array_filter(
                    array_column($doseFormGroups, 'doseFormGroupName')
                )
            )
        );
    }

    public static function isValidRxcui(string $rxcui): bool
    {
        if (empty($rxcui)) {
            return false;
        }

        $cacheKey = "rxcui:{$rxcui}";
        $ttl = config('cache.ttl.drug_search', 3600);

        return Cache::remember($cacheKey, $ttl, function () use ($rxcui) {
            try {
                $url = sprintf(self::GET_DRUG_DETAILS_URL, $rxcui);
                $response = Http::timeout(self::$timeout)->get($url);

                if (!$response->successful()) {
                    return false;
                }

                $data       = $response->json();
                $validRxcui = data_get($data, 'rxcuiStatusHistory.attributes.rxcui', '');
                $name       = data_get($data, 'rxcuiStatusHistory.attributes.name', '');

                return $rxcui === $validRxcui && !empty($name);
            } catch (Exception $ex) {
                HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());
                return false;
            }
        });
    }
}
