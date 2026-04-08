<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\SocialWorkerAppointmentController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;
use App\Http\Controllers\ChildWeekController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\ChildMessageController;

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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:young_person'])->group(function () {
    Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])
        ->name('child.dashboard');

    Route::post('/child/mood-checkin', [MoodCheckinController::class, 'store'])
        ->name('child.mood.store');

    Route::get('/child/goals', [ChildGoalsController::class, 'index'])
        ->name('child.goals.index');

    Route::post('/child/goals', [ChildGoalsController::class, 'store'])
        ->name('child.goals.store');

    Route::get('/child/trusted-people', [TrustedPeopleController::class, 'index'])
        ->name('child.trusted.index');

    Route::post('/child/trusted-people', [TrustedPeopleController::class, 'store'])
        ->name('child.trusted.store');

    Route::get('/child/week', [ChildWeekController::class, 'index'])
        ->name('child.week.index');

    Route::post('/child/support-request', [SupportRequestController::class, 'store'])
        ->name('child.support.store');

    Route::get('/child/diary', [DiaryEntryController::class, 'index'])
        ->name('child.diary.index');

    Route::post('/child/diary', [DiaryEntryController::class, 'store'])
        ->name('child.diary.store');

    Route::get('/child/messages', [ChildMessageController::class, 'index'])
        ->name('child.messages.index');

    Route::post('/child/messages', [ChildMessageController::class, 'store'])
        ->name('child.messages.store');
});

Route::middleware(['auth', 'role:social_worker'])->group(function () {
    Route::get('/socialworker/dashboard', [SocialWorkerDashboardController::class, 'index'])
        ->name('socialworker.dashboard');

    Route::get('/socialworker/appointments', [SocialWorkerAppointmentController::class, 'index'])
        ->name('socialworker.appointments.index');

    Route::post('/socialworker/appointments', [SocialWorkerAppointmentController::class, 'store'])
        ->name('socialworker.appointments.store');

    Route::get('/socialworker/case-files', [CaseFileController::class, 'index'])
        ->name('socialworker.casefiles.index');

    Route::post('/socialworker/case/{case}/assign-carer', [CaseFileController::class, 'assignCarer'])
        ->name('case.assignCarer');

    Route::post('/socialworker/case/{case}/placement', [CaseFileController::class, 'storePlacement'])
        ->name('case.addPlacement');

    Route::post('/socialworker/case/{case}/medical', [CaseFileController::class, 'storeMedical'])
        ->name('case.addMedical');

    Route::post('/socialworker/case/{case}/education', [CaseFileController::class, 'storeEducation'])
        ->name('case.addEducation');

    Route::post('/socialworker/case/{case}/document', [CaseFileController::class, 'storeDocument'])
        ->name('case.addDocument');
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

require __DIR__.'/auth.php';
require __DIR__.'/carer.php';
require __DIR__.'/socialworker.php';