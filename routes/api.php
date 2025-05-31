<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\WalletController;
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
        Route::get('balance', [WalletController::class, 'balance']);
        Route::get('transactions', [WalletController::class, 'transactions']);
    });

    Route::prefix('order')->group(function () {
        Route::post('place', [OrderController::class, 'place']);
        Route::post('{order}/cancel', [OrderController::class, 'cancel']);
        Route::get('{order}/show', [OrderController::class, 'show']);
    });

});
