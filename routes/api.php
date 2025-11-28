<?php

declare(strict_types=1);

// routes/api.php

use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

/* Route::post('/register', [AuthController::class, 'register']); */
/* Route::post('/login', [AuthController::class, 'login']); */

Route::get('/coupons/{id}', [CouponController::class, 'show']);
Route::get('/coupons', [CouponController::class, 'index']);
Route::get('/coupons/available/{operatorId}/{planTypeId}', [CouponController::class, 'available']);
Route::post('/coupons/purchase', [CouponController::class, 'purchase']);
Route::get('/coupons/transactions', [CouponController::class, 'transactionHistory']);

Route::middleware('auth:sanctum')->group(function (): void {
    /* Route::get('/user', [AuthController::class, 'user']); */
    /* Route::post('/logout', [AuthController::class, 'logout']); */

    // Wallet routes
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/add-balance', [WalletController::class, 'addBalance']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactionHistory']);

    // Country and operator routes
    Route::get('/countries', [CountryController::class, 'index']);
    Route::get('/countries/{countryId}/operators', [CountryController::class, 'operators']);

    Route::prefix('media')->group(function (): void {
        Route::post('/{modelType}/{modelId}/upload', [MediaController::class, 'upload']);
        Route::post('/{modelType}/{modelId}/upload-from-url', [MediaController::class, 'uploadFromUrl']);
        Route::get('/{modelType}/{modelId}/images', [MediaController::class, 'getImages']);
        Route::delete('/{modelType}/{modelId}/images/{mediaId}', [MediaController::class, 'deleteImage']);
        Route::delete('/{modelType}/{modelId}/collection', [MediaController::class, 'clearCollection']);
    });

});
