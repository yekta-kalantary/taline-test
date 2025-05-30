<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('guest')->group(function () {
    Route::post('auth/login', LoginController::class);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::prefix('wallet')->group(function () {
        Route::get('balance', [\App\Http\Controllers\WalletController::class, 'balance']);
        Route::get('transactions', [\App\Http\Controllers\WalletController::class, 'transactions']);
    });

});
