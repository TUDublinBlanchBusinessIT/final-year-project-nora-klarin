<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;
use App\Http\Controllers\CarerMessageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\AdminUserController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    // Redirect based on role
    switch ($user->role) {
        case 'carer':
            return redirect()->route('carer.dashboard');
        case 'social_worker':
            return redirect()->route('socialworker.dashboard');
        case 'admin':
            return redirect()->route('admin.users.index');
        default:
            abort(403, 'Unauthorized');
    }
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

Route::middleware(['auth', 'role:social_worker'])->group(function () {
    Route::get('/social-worker/dashboard', 
        [SocialWorkerDashboardController::class, 'index']
    )->name('socialworker.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
});

Route::middleware(['auth', 'role:social_worker'])->group(function () {
    Route::get('/social-worker/case/{case}', [SocialWorkerDashboardController::class, 'show'])
        ->name('socialworker.case.show');
});


require __DIR__.'/auth.php';

