<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;
use App\Http\Controllers\CarerMessageController;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SocialWorkerAppointmentController;


use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;
use App\Http\Controllers\ChildWeekController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\DiaryEntryController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();


    if (!$user) {
        return redirect()->route('login');
    }

    return match ($user->role) {
        'carer' => redirect()->route('carer.dashboard'),
        'social_worker' => redirect()->route('socialworker.dashboard'),
        'admin' => redirect()->route('admin.users.index'),
        'young_person' => redirect()->route('child.dashboard'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

    Route::get('/carer/messages', [CarerMessageController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.index');

Route::get('/carer/messages/create', [CarerMessageController::class, 'create'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.create');

Route::post('/carer/messages', [CarerMessageController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('carer.messages.store');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:young_person'])->group(function () {
    Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])
        ->name('child.dashboard');
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


Route::middleware(['auth'])->group(function () {

    Route::get(
        '/social-worker/case/{case}/appointments/create',
        [SocialWorkerAppointmentController::class, 'create']
    )->name('social-worker.appointments.create');

    Route::post(
        '/social-worker/case/{case}/appointments',
        [SocialWorkerAppointmentController::class, 'store']
    )->name('social-worker.appointments.store');

});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

require __DIR__.'/auth.php';
require __DIR__.'/carer.php';
require __DIR__.'/socialworker.php';


