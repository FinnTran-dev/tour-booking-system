<?php

use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\PassengerController;
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
    Route::get('/tours/{tour}/bookings', [TourController::class, 'bookings']);

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::patch('/bookings/{booking}', [BookingController::class, 'update']);

    // Passengers
    Route::get('/passengers', [PassengerController::class, 'index']);
    Route::post('/passengers', [PassengerController::class, 'store']);
    Route::get('/passengers/{passenger}', [PassengerController::class, 'show']);
    Route::put('/passengers/{passenger}', [PassengerController::class, 'update']);
    Route::patch('/passengers/{passenger}', [PassengerController::class, 'update']);
    Route::delete('/passengers/{passenger}', [PassengerController::class, 'destroy']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update']);
});
