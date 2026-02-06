<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;
use App\Http\Controllers\CarerMessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    // Redirect carers to their dashboard
    if ($user && $user->role === 'carer') {
        return redirect()->route('carer.dashboard');
    }

    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Carer Routes
|--------------------------------------------------------------------------
*/
Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('carer.dashboard');

Route::get('/carer/calendar', [CarerCalendarController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('carer.calendar');

    Route::get('/carer/messages', [CarerMessageController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.index');

Route::get('/carer/messages/create', [CarerMessageController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.create');

Route::post('/carer/messages', [CarerMessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.store');


/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

