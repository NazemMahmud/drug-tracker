<?php

namespace Tests\Unit;

use App\Helpers\Constants;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class DrugSearchTest extends TestCase
{
    private string $baseUrl;
    private string $drugName;

    /**
     * Pre-set test data before test methods call
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        /**
         * got the drug name from internet
         * tried to get some names from: https://www.nlm.nih.gov/research/umls/rxnorm/docs/rxnormfiles.html,
         * but could not download
         */
        $this->drugName = "Amoxicillin";

        $this->baseUrl = env('APP_URL') . '/api/drug-search?drug_name=';
    }

    /**
     * Test 1: query param is not sent
     * @return void
     */
    public function test_query_param_unavailable(): void
    {
        $response = $this->getJson($this->baseUrl);
        $response->assertStatus(422)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::ERROR_DRUG_NAME_REQUIRED);
    }

    /**
     * Test 2: Success
     * @return void
     */
    public function test_success_drug_search(): void
    {
        $response = $this->getJson($this->baseUrl . $this->drugName);

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' =>  [
                    '*' => [
                        'rxcui','name', 'base_names', 'dose_form_group_names'
                    ],
                ],
                'status'
            ])->assertJsonPath('status', Constants::SUCCESS);
    }
}
