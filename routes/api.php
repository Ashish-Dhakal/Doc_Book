<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\DoctorController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SpecialityController;
use App\Http\Controllers\Api\V1\AppointmentController;
use App\Http\Controllers\Api\V1\AppointmentSlotController;




// API Routes
Route::prefix('V1')->group(function () {

    // Auth Routes
    Route::post('register', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login']);

    // Protected Routes 
    Route::middleware('auth:sanctum' , 'throttle:60,1')->group(function () {

        // Auth Routes
        Route::post('logout', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'logout']);

        //route name() prefix

        // Route name() prefix
        Route::name('api.')->group(function () {

            // Apply middleware 'role:admin' only to the 'users' resource
            Route::middleware('role:admin')->group(function () {
                Route::apiResource('users', UserController::class);
                Route::apiResource('specializations', SpecialityController::class);
            });

            Route::middleware('role:admin,doctor')->group(function () {
                Route::apiResource('appountmentSlots',AppointmentSlotController::class);
            });

            Route::apiResource('appointments', AppointmentController::class);
            Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

            // Route::middleware('role:patient,doctor')->group(function () {
                Route::apiResource('reviews', ReviewController::class);
            // });

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

            Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');

            Route::get('/payment/{id}/pay', [PaymentController::class, 'pay'])->name('payment.pay');
            Route::post('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
            Route::post('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');


        });
    });
});
