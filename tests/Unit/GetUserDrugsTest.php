<?php

namespace Tests\Unit;

use App\Helpers\Constants;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class GetUserDrugsTest extends TestCase
{
    private string $baseUrl;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->token   = Redis::get('test_access_token');
        $this->baseUrl = env('APP_URL') . '/api/user/drugs';
    }

    /**
     * Test 1: Unauthenticated user/ Token invalid
     */
    public function test_invalid_token()
    {
        $response = $this->getJson($this->baseUrl);
        $response->assertStatus(JsonResponse::HTTP_NOT_FOUND)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::TOKEN_NOT_FOUND);
    }


    /**
     * Test 2: successfully get medication list
     */
    public function test_success_get_medication()
    {
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])->getJson($this->baseUrl);

        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ 'rxcui','name', 'base_names', 'dose_form_group_names']
                ],
                'status'
            ])
            ->assertJsonPath('status', Constants::SUCCESS);
    }
}
