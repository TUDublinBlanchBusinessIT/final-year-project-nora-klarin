<?php
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;
Route::middleware(['auth', 'role:carer'])->group(function () {
    Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])
        ->name('carer.dashboard');

    Route::get('/carer/calendar', [CarerCalendarController::class, 'index'])
        ->name('carer.calendar');

    Route::resource('/carer/messages', CarerMessageController::class);
});
