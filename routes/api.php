<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('/v1')->group(function () {
    Route::post('/register',    [AuthController::class,     'register']);
    Route::post('/login',       [AuthController::class,     'login']);
    Route::post('/webhook',     [WebhookController::class,  'index']);

    Route::middleware('tokenValid')->group(function () {
        Route::get('/payments/{user_id}',   [PaymentController::class,  'showPayments']);

        Route::post('/create-debt',         [PaymentController::class,  'createDebt']);
        Route::post('/logout',              [AuthController::class,     'logout']);
    });
});
