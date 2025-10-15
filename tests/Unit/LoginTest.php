<?php

namespace Tests\Unit;

use App\Helpers\Constants;

use Illuminate\Http\JsonResponse;
use Tests\TestCase;


class LoginTest extends TestCase
{
    /** API URL for login */
    private string $loginUrl;

    /** Test data for: form validation error */
    private array $invalidData;

    /** Test data for: validation error for incorrect email */
    private array $invalidEmailData;

    /** Test data for: validation errors for incorrect password */
    private array $invalidPasswordData;

    /** Test data for: successfully register a new user */
    private array $successRequestData;

    /**
     * Pre-set test data before test methods call
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUrl = env('APP_URL') . '/api/login';

        $this->invalidEmailData = [
            'email'    => 'abc@gmail.com',
            'password' => 'password123'
        ];

        $this->invalidPasswordData = [
            'email'    => 'john@email.com',
            'password' => '1234567890'
        ];

        $this->invalidData = [
            'email'    => 'unittest1',
            'password' => '123456'
        ];

        $this->successRequestData = [
            'email'    => 'john@email.com',
            'password' => 'password123'
        ];
    }

    /**
     * Test 1: validation error for incorrect email
     * @return void
     */
    public function test_invalid_email(): void
    {
        $response = $this->postJson($this->loginUrl, $this->invalidEmailData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::ERROR_INVALID_LOGIN);
    }

    /**
     * Test 2: validation errors (multiple) for incorrect password
     * @return void
     */
    public function test_invalid_password(): void
    {
        $response = $this->postJson($this->loginUrl, $this->invalidPasswordData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::ERROR_INVALID_LOGIN);
    }

    /**
     * Test 3: validation error for incorrect email
     * @return void
     */
    public function test_invalid_form_data(): void
    {
        $response = $this->postJson($this->loginUrl, $this->invalidData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', "The email field must be a valid email address.");;
    }

    /**
     * Test 4: successfully register a new user
     * Set access token in cache here for other auth test route can use
     * Expire the cache after 1 hour
     * @return void
     */
    public function test_success_login(): void
    {
        $response = $this->postJson($this->loginUrl, $this->successRequestData);
        $response->assertStatus(JsonResponse::HTTP_OK)
            ->assertJsonStructure([
                'data' => ['access_token', 'expires_in'],
                'status'
            ])->assertJsonPath('status', Constants::SUCCESS);

        // todo: add in redis later for testing purpose
    }
}
