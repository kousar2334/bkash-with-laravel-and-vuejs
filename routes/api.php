<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BkashController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//bkash payment
Route::post('/bkash-payment-token-create', [BkashController::class, 'bkashPaymentToken']);
Route::post('/bkash-create-payment-request', [BkashController::class, 'createpayment']);
Route::post('/bkash-execute-payment-request', [BkashController::class, 'executepayment']);
