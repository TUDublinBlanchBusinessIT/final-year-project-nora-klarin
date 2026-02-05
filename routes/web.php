<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\ChildDashboardController;
use Illuminate\Http\Request;
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

    if ($user && $user->role === 'carer') {
        return redirect()->route('carer.dashboard');
    }

    if ($user && $user->role === 'child') {
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
