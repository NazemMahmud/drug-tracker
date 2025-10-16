<?php

use App\Http\Controllers\{ AuthController, DrugsController, UsersDrugController };
use App\Http\Middleware\JWTMiddleware;

use Illuminate\Support\Facades\Route;


Route::middleware(['throttle:auth'])->group(function () {
    Route::post('register', [AuthController::class, 'registration']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::middleware(['throttle:drug-search'])->group(function () {
    Route::get('drug-search', [DrugsController::class, 'searchDrugs']);
});

Route::middleware([JWTMiddleware::class, 'throttle:user-drugs'])->prefix('user')->group(function () {
    Route::controller(UsersDrugController::class)->prefix('drugs')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::delete('/{rxcui}', 'destroy');
    });
});
