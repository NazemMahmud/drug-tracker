<?php

use App\Http\Controllers\{ AuthController, DrugsController, UsersDrugController };
use App\Http\Middleware\JWTMiddleware;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/**
 * todo: remove at the end of project tasks
 */
Route::get('/health', function () {
    try {
        DB::connection()->getPdo();
        $databaseStatus = 'connected';
    } catch (\Exception $e) {
        $databaseStatus = 'disconnected';
    }
    return response()->json([
        'status' => 'healthy',
        'service' => 'Drug Tracker API',
        'timestamp' => now()->toIso8601String(),
        'database' => $databaseStatus,
    ]);
});

Route::post('register', [AuthController::class, 'registration']);
Route::post('login', [AuthController::class, 'login']);

Route::get('drug-search', [DrugsController::class, 'searchDrugs']);

Route::middleware([JWTMiddleware::class])->prefix('user')->group(function () {
    Route::controller(UsersDrugController::class)->prefix('drugs')->group(function () {
        Route::post('/', 'store');
        Route::get('/', 'index');
        Route::delete('/{rxcui}', 'destroy');
    });
});
