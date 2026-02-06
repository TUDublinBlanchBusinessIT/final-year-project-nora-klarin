<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\MoodCheckinController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect (ROLE BASED)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user?->role === 'carer') {
        return redirect()->route('carer.dashboard');
    }

    if ($user?->role === 'child') {
        return redirect()->route('child.dashboard');
    }

    // Fallback (default Breeze dashboard)
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Carer Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('carer.dashboard');

/*
|--------------------------------------------------------------------------
| Child Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('child.dashboard');

/*
|--------------------------------------------------------------------------
| Child Mood Check-in (emoji click -> SAVES TO DB)
|--------------------------------------------------------------------------
| Your buttons can keep using: route('child.mood.save', 'happy')
*/
Route::get('/child/mood/{mood}', [MoodCheckinController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('child.mood.save');

/*
|--------------------------------------------------------------------------
| Child Diary (DEMO SAVE FOR NOW)
|--------------------------------------------------------------------------
*/
Route::post('/child/diary', function (Request $request) {
    return back()->with('success', 'Diary entry saved (demo only)');
})->middleware(['auth', 'verified'])->name('child.diary.store');

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

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
