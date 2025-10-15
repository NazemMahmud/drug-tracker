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
}
