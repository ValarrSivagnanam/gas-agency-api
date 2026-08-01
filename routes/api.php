<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CylinderController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\ReportController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/cylinders', [CylinderController::class, 'index']);
    Route::put('/cylinders/{id}/price', [CylinderController::class, 'updatePrice']);
    Route::post('/cylinders/{id}/transaction', [CylinderController::class, 'recordTransaction']);

    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
    Route::get('/customers/{id}/transactions', [CustomerController::class, 'transactions']);

    Route::get('/dashboard/summary', [ReportController::class, 'dashboardSummary']);
    Route::get('/transactions', [ReportController::class, 'index']);
    Route::get('/transactions/{id}', [ReportController::class, 'show']);
});