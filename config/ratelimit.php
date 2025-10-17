<?php

return [
    'auth'        => env('RATE_LIMIT_AUTH', 5),
    'drug_search' => env('RATE_LIMIT_DRUG_SEARCH', 20),
    'user_drugs'  => env('RATE_LIMIT_USER_DRUGS', 30),
    'default'     => env('RATE_LIMIT_DEFAULT', 60),
];
