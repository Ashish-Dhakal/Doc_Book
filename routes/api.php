<?php

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

        Route::apiResource('/users', UserController::class)->names([
            'index' => 'api.users.index',
            'store' => 'api.users.store',
            'show' => 'api.users.show',
            'update' => 'api.users.update',
            'destroy' => 'api.users.destroy',
        ]);

    });

});
