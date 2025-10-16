<?php

namespace Tests\Unit;

use App\Helpers\Constants;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class AddUserDrugTest extends TestCase
{
    /** API URL for store a new IP address */
    private string $baseUrl;

    /** Authentication token */
    private string $token;

    /** Test data for: successfully add mediation to user */
    private array $successData;

    /** Test data for: missing field value */
    private array $missingData;

    /** Test data for: data type mismatch for field value */
    private array $wrongDataType;

    /** Test data for: rxcui data not found in national library DB */
    private array $invalidData;

    /**
     * Pre-set test data before test methods call
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // todo
    }

    /**
     * Test 1: Unauthenticated user/ Token invalid
     */
    public function test_invalid_token()
    {
        // todo
    }

    /**
     * Test 2: validation error: missing rxcui value
     */
    public function test_value_missing()
    {
        // todo

    }

    /**
     * Test 3: validation error: type mismatch for rxcui
     */
    public function test_data_type_mismatch()
    {
        // todo
    }

    /**
     * Test 4: invalid rxcui from library DB
     */
    public function test_invalid_data()
    {
        // todo
    }

    /**
     * Test 5: successfully added
     */
    public function test_success_search()
    {
        // todo
    }

    /**
     * Test 6: already added
     */
    public function test_already_added()
    {
        // todo
    }
}
