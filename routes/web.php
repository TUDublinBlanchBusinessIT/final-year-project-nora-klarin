<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;
use App\Http\Controllers\ChildWeekController;
use App\Http\Controllers\SupportRequestController;

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
| Child Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])
        ->name('child.dashboard');

    Route::get('/child/mood/{mood}', [MoodCheckinController::class, 'store'])
        ->name('child.mood.save');

    Route::get('/child/goals', [ChildGoalsController::class, 'index'])
        ->name('child.goals');

    Route::post('/child/goals', [ChildGoalsController::class, 'store'])
        ->name('child.goals.store');

    Route::get('/child/trusted-people', [TrustedPeopleController::class, 'index'])
        ->name('child.trusted');

    Route::post('/child/trusted-people', [TrustedPeopleController::class, 'store'])
        ->name('child.trusted.store');

    Route::get('/child/week', [ChildWeekController::class, 'index'])
        ->name('child.week');

    /*
    |----------------------------------------------------------------------
    | Need Help (Support Request)
    |----------------------------------------------------------------------
    */
    Route::get('/child/support', [SupportRequestController::class, 'index'])
        ->name('child.support');

    Route::post('/child/support', [SupportRequestController::class, 'store'])
        ->name('child.support.store');

    // Child diary (demo)
    Route::post('/child/diary', function (Request $request) {
        return back()->with('success', 'Diary entry saved (demo only)');
    })->name('child.diary.store');
});

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


