<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication
use App\Http\Controllers\API\AuthController;

// Public gas agency endpoint
Route::post('/login', [AuthController::class, 'login']);

// Authenticated endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Stock Management Endpoints
    Route::get('/cylinders', [\App\Http\Controllers\API\CylinderController::class, 'index']);
    Route::post('/cylinders/{id}/transaction', [\App\Http\Controllers\API\CylinderController::class, 'recordTransaction']);

    // Customer Management Endpoints
    Route::get('/customers', [\App\Http\Controllers\API\CustomerController::class, 'index']);
    Route::post('/customers', [\App\Http\Controllers\API\CustomerController::class, 'store']);
    Route::put('/customers/{id}', [\App\Http\Controllers\API\CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [\App\Http\Controllers\API\CustomerController::class, 'destroy']);
});
