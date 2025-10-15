<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\HttpHandler;

use Illuminate\Support\Facades\Http;

use Exception;

class DrugService
{
    protected int $timeout = 100;
    protected string $tty  = 'SBD';

    protected string $getDrugsURL       = 'https://rxnav.nlm.nih.gov/REST/drugs.json?name=%s';
    protected string $getDrugDetailsURL = "https://rxnav.nlm.nih.gov/REST/rxcui/%s/historystatus.json";


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
        try {
            $drugInfo = $this->getDrugs($drugName);
            $drugData = $this->extractDrugInfo($drugInfo, $drugName, $limit);
            $details  = $this->getDrugDetails($drugData, $drugName);
        } catch (Exception $ex) {
            HttpHandler::errorLogMessageHandler($ex->getMessage(), $ex->getCode());
            throw new Exception("Error fetching drug information for $drugName", $ex->getCode());
        }

        return $details;
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
            $url = sprintf($this->getDrugsURL, $drugName);
            $response = Http::timeout(10)->get($url);

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
    public function extractDrugInfo(array $drugInfo, string $drugName, int $limit): array
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
    public function getDrugDetails(array $drugData, string $drugName): array
    {
        if (empty($drugData)) {
            return [];
        }

        $result    = [];
        $responses = Http::pool(function ($pool) use ($drugData) {
            $promises = [];
            foreach ($drugData as $index => $drug) {
                $url = sprintf($this->getDrugDetailsURL, $drug['rxcui']);
                $promises[$index] = $pool->get($url);
            }
            return $promises;
        });

        foreach ($responses as $index => $response) {
            $drug = $drugData[$index];
            $rxcui = $drug['rxcui'];

            if ($response->successful()) {
                $details = $response->json();

                $result[] = [
                    'rxcui'                 => $rxcui,
                    'name'                  => $drug['name'],
                    'drug_name'             => $drugName,
                    'base_names'            => $this->extractBaseNames($details),
                    'dose_form_group_names' => $this->extractDoseFormGroups($details),
                ];
            } else {
                $errorMessage = "Failed to fetch drug details: ";
                $errorData = [
                    'drug_name' => $drugName,
                    'rxcui'     => $rxcui,
                    'status'    => $response->status(),
                ];
                HttpHandler::errorHandler($errorMessage, $errorData);
            }
        }

        return $result;
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
}
