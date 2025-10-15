<?php

use App\Http\Controllers\{ AuthController, DrugsController };

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

Route::post('register', [AuthController::class, 'registration'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::get('drug-search', [DrugsController::class, 'searchDrugs'])->name('drug-search');
