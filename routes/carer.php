<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;
use App\Http\Controllers\CarerMessageController;

Route::middleware(['auth', 'role:carer'])->group(function () {
    Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])
        ->name('carer.dashboard');

    Route::get('/carer/calendar', [CarerCalendarController::class, 'index'])
        ->name('carer.calendar');

Route::resource('messages', CarerMessageController::class)
    ->names('carer.messages');
});
