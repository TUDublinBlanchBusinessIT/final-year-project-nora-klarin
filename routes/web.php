<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;

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
| Mood Check-in
|--------------------------------------------------------------------------
*/
Route::get('/child/mood/{mood}', [MoodCheckinController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('child.mood.save');

/*
|--------------------------------------------------------------------------
| Child Goals
|--------------------------------------------------------------------------
*/
Route::get('/child/goals', [ChildGoalsController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('child.goals');

Route::post('/child/goals', [ChildGoalsController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('child.goals.store');

/*
|--------------------------------------------------------------------------
| Trusted People (CONNECTED TO DB)
|--------------------------------------------------------------------------
*/
Route::get('/child/trusted-people', [TrustedPeopleController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('child.trusted');

Route::post('/child/trusted-people', [TrustedPeopleController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('child.trusted.store');

/*
|--------------------------------------------------------------------------
| Child Week (still simple)
|--------------------------------------------------------------------------
*/
Route::get('/child/week', function () {
    return view('child.week');
})->middleware(['auth', 'verified'])->name('child.week');

/*
|--------------------------------------------------------------------------
| Child Diary (demo)
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

require __DIR__ . '/auth.php';
