<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AppointmentSlotController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Models\AppointmentSlot;

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
    Route::resource('/users', UserController::class);
    Route::resource('/appointment-slots', AppointmentSlotController::class);
    Route::resource('/appointments', AppointmentController::class);
});

require __DIR__.'/auth.php';
