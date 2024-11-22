<?php

use App\Models\AppointmentSlot;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleCheckMiddleware;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentSlotController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');




    // route for creatind user
    Route::resource('/users', UserController::class)->middleware('role:admin');
    Route::resource('/appointment-slots', AppointmentSlotController::class)->middleware('role:admin,doctor');
    Route::resource('/appointments', AppointmentController::class);
    Route::resource('/specializations', SpecialityController::class)->middleware('role:admin');


    Route::post('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    Route::resource('/reviews', ReviewController::class);


});

require __DIR__.'/auth.php';
