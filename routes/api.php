<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Webhooks\PaymentWebhookController;


/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */


Route::prefix('v1')->group(function () {


    Route::post(
        '/register',
        [AuthController::class, 'register']
    );

    Route::post(
        '/login',
        [AuthController::class, 'login']
    );

    Route::middleware('auth:sanctum')
        ->group(function () {

            Route::get(
                '/me',
                [AuthController::class, 'me']
            );

            Route::post(
                '/logout',
                [AuthController::class, 'logout']
            );
        });
        

    Route::post(
        '/webhooks/payment',
        [PaymentWebhookController::class, 'handle']
    )->name('webhooks.payment');
});