<?php

use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\TourController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Tours
    Route::get('/tours', [TourController::class, 'index']);
    Route::post('/tours', [TourController::class, 'store']);
    Route::get('/tours/{tour}', [TourController::class, 'show']);
    Route::put('/tours/{tour}', [TourController::class, 'update']);
    Route::patch('/tours/{tour}', [TourController::class, 'update']);

    // Bookings
    Route::post('/bookings', [BookingController::class, 'store']);
});
