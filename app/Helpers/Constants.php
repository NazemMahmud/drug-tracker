<?php

declare(strict_types=1);

namespace App\Helpers;

abstract class Constants
{
    public const SUCCESS = 'success';
    public const FAILED  = 'failed';

    public const ERROR_DB             = 'Database error.';
    public const SOMETHING_WENT_WRONG = 'Something went wrong';
    public const NOT_FOUND            = 'Data not found';
    public const INVALID_TOKEN        = 'Token is Invalid';
    public const EXPIRED_TOKEN        = 'Token is Expired';
    public const TOKEN_NOT_FOUND      = 'Token not found';

    /**
     * Registration related
     */
    public const ERROR_REGISTER = 'Registration failed';
    public const USER_EXISTS = 'User already exists.';
    public const SUCCESS_REGISTER = 'Registration done successfully.';
    public const ERROR_INVALID_LOGIN = 'Invalid email or password';

    /**
     * Drug search related
     */
    public const ERROR_DRUG_NAME_REQUIRED = 'Drug name is required.';

    /**
     * Users drugs related
     */
    public const USER_DRUG_EXIST     = 'This drug is already added for this user.';
    public const ERROR_DB_CREATE     = 'Failed to create record';
    public const ERROR_DB_DELETE     = 'Failed to delete record';
    public const SUCCESS_DRUG_ADD    = 'Drug added successfully for the user';
    public const USER_DRUG_NOT_FOUND = 'Drug not found in the users medication list';
    public const USER_DRUG_DELETED   = 'Drug removed from your medication list successfully';
    public const ERROR_DB_READ       = 'Failed to retrieve records';
}
