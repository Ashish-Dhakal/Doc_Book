<?php

use App\Http\Controllers\Api\V1\AppointmentSlotController;
use App\Http\Controllers\Api\V1\SpecialityController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




// API Routes
Route::prefix('V1')->group(function () {

    // Auth Routes
    Route::post('register', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'register']);
    Route::post('login', [App\Http\Controllers\Api\V1\Auth\AuthController::class, 'login']);

    // Protected Routes 
    Route::middleware('auth:sanctum')->group(function () {

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


        });
    });
});
