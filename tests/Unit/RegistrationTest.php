<?php

namespace Tests\Unit;

use App\Helpers\Constants;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    private string $registrationUrl;
    private array $invalidNameData;
    private array $invalidEmailData;
    private array $invalidPasswordData;
    private array $validUserData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrationUrl = env('APP_URL') . '/api/register';

        $this->invalidNameData = [
            'name'     => '',
            'email'    => 'unittest1@gmail.com',
            'password' => '123456'
        ];

        $this->invalidEmailData = [
            'name'     => 'Unit Test User 1',
            'email'    => 'unittest1',
            'password' => '123456'
        ];

        $this->invalidPasswordData = [
            'name'     => 'Unit Test User 1',
            'email'    => 'unittest2@gmail.com',
            'password' => '123'
        ];

        $this->validUserData = [
            'name'     => 'John Doe',
            'email'    => 'john@email.com',
            'password' => 'password123'
        ];
    }

    /**
     * Test 1: validation error for name
     * @return void
     */
    public function test_name_not_validated(): void
    {
        $response = $this->postJson($this->registrationUrl, $this->invalidNameData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', "The name field is required.");
    }

    /**
     * Test 2: validation error for email
     * @return void
     */
    public function test_email_not_validated(): void
    {
        $response = $this->postJson($this->registrationUrl, $this->invalidEmailData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message',  "The email field must be a valid email address.");
    }

    /**
     * Test 3: validation error for password
     * @return void
     */
    public function test_password_not_validated(): void
    {
        $response = $this->postJson($this->registrationUrl, $this->invalidPasswordData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', "The password field must be at least 6 characters.");
    }

    /**
     * Test 4: successfully register a new user
     * @return void
     */
    public function test_success_registration(): void
    {
        User::where('email', $this->validUserData['email'])->delete();

        $response = $this->postJson($this->registrationUrl, $this->validUserData);
        $response->assertStatus(JsonResponse::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [ 'message'],
                'status'
            ])->assertJsonPath('status', Constants::SUCCESS)
            ->assertJsonPath('data.message', Constants::SUCCESS_REGISTER);
    }

    /**
     * Test 5: duplicate email error test
     * @return void
     */
    public function test_duplicate_email_error(): void
    {
        $response = $this->postJson($this->registrationUrl, $this->validUserData);
        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['data', 'message', 'status'])
            ->assertJsonPath('status', Constants::FAILED)
            ->assertJsonPath('message', Constants::USER_EXISTS);
    }
}
