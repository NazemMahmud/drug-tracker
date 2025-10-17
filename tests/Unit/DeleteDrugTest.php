<?php

namespace Tests\Unit;

use App\Helpers\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class DeleteDrugTest extends TestCase
{
    private string $baseUrl;
    private string $token;
    private string $successData;
    private string $notFoundData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token        = Redis::get('test_access_token');
        $this->baseUrl      = env('APP_URL') . '/api/user/drugs/';
        $this->notFoundData = '100';
        $this->successData  = '997484';
    }

    /**
     * Test 1: Unauthenticated user/ Token invalid
     */
    public function test_invalid_token()
    {
        $response = $this->deleteJson($this->baseUrl . $this->notFoundData);
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::TOKEN_NOT_FOUND);
    }

    /**
     * Test 2: invalid rxcui from library DB
     */
    public function test_drug_not_for_user()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson($this->baseUrl . $this->notFoundData);

        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::USER_DRUG_NOT_FOUND);
    }

    /**
     * Test 3: successfully deleted (soft delete)
     */
    public function test_success_soft_delete()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->deleteJson($this->baseUrl . $this->successData);

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' => ['message'],
                'status'
            ])
            ->assertJsonPath('status', Constants::SUCCESS)
            ->assertJsonPath('data.message', Constants::USER_DRUG_DELETED);
    }

}
