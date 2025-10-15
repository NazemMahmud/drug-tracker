<?php

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;


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
