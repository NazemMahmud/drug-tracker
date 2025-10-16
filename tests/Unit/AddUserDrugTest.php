<?php

namespace Tests\Unit;

use App\Helpers\Constants;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class AddUserDrugTest extends TestCase
{
    private string $baseUrl;
    private string $token;
    private array $successData;
    private array $missingData;
    private array $wrongDataType;
    private array $invalidData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token   = Redis::get('test_access_token');
        $this->baseUrl = env('APP_URL') . '/api/user/drugs';

        $this->missingData = [
            'rxcui' => '',
        ];

        $this->wrongDataType = [
            'rxcui' => 997484,
        ];

        $this->invalidData = [
            'rxcui' => '001'
        ];

        $this->successData = [
            'rxcui' => '997484',
        ];
    }

    /**
     * Test 1: Unauthenticated user/ Token invalid
     */
    public function test_invalid_token()
    {
        $response = $this->postJson($this->baseUrl, $this->successData);
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::TOKEN_NOT_FOUND);
    }

    /**
     * Test 2: validation error: missing rxcui value
     */
    public function test_value_missing()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson($this->baseUrl, $this->missingData);

        $response->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', 'The drug rxcui field is required.');

    }

    /**
     * Test 3: validation error: type mismatch for rxcui
     */
    public function test_data_type_mismatch()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson($this->baseUrl, $this->wrongDataType);

        $response->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', 'The drug rxcui value must be a valid string.');
    }

    /**
     * Test 4: invalid rxcui from library DB
     */
    public function test_invalid_data()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson($this->baseUrl, $this->invalidData);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::ERROR_INVALID_DRUG);
    }

    /**
     * Test 5: successfully added
     */
    public function test_success_add_drug()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson($this->baseUrl, $this->successData);

        $response->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => ['id', 'user_id', 'rxcui'],
                'status'
            ])
            ->assertJsonPath('status', Constants::SUCCESS);
    }

    /**
     * Test 6: already added
     */
    public function test_already_added()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->postJson($this->baseUrl, $this->successData);

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' => ['message'],
                'status'
            ])
            ->assertJsonPath('status', Constants::SUCCESS)
            ->assertJsonPath('data.message', Constants::USER_DRUG_EXIST);
    }
}
