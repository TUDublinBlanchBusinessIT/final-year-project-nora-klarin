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
use App\Http\Controllers\CaseFileController;
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

        Route::get('/social-worker/case/{case}/show', [CaseFileController::class, 'show'])
            ->name('socialworker.case.show');

        Route::get('/social-worker/case/{case}/edit', [CaseFileController::class, 'edit'])
            ->name('socialworker.case.edit');

        Route::put('/social-worker/case/{case}/update', [CaseFileController::class, 'update'])
            ->name('socialworker.case.update');

        Route::post('/social-worker/case/{case}/placements', [PlacementController::class, 'store'])
    ->name('placements.store');

        Route::post('/social-worker/case/{case}/medical', [CaseFileController::class, 'storeMedical'])->name('cases.medical.store');
        
        Route::post('/social-worker/case/{case}/education', [CaseFileController::class, 'storeEducation'])->name('cases.education.store');
    
        Route::post('/social-worker/case/{case}/documents', [CaseFileController::class, 'storeDocument'])->name('cases.documents.store');

        Route::post('/social-worker/case/{case}/placements', [CaseFileController::class, 'store'])
        ->name('case.addPlacement');

        
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
});

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

    Route::get('/social-worker/cases', [SocialWorkerDashboardController::class, 'list'])
    ->middleware(['auth','role:social_worker'])
    ->name('socialworker.case.list');

    Route::post('/cases/{case}/assign-carer', [CaseFileController::class, 'assignCarer'])
    ->name('case.assignCarer')
    ->middleware('auth');

        Route::get('/child/support', [SupportRequestController::class, 'index'])
        ->name('child.support');

    Route::post('/child/support', [SupportRequestController::class, 'store'])
        ->name('child.support.store');

    /*
    |--------------------------------------------------------------------------
    | ✅ Diary (NOW SAVES TO DB)
    |--------------------------------------------------------------------------
    */
    Route::post('/child/diary', [DiaryEntryController::class, 'store'])
        ->name('child.diary.store');

    /*
    |--------------------------------------------------------------------------
    | ✅ Child Messages (Step 4)
    |--------------------------------------------------------------------------
    */
    Route::get('/child/messages', [ChildMessageController::class, 'index'])
        ->name('child.messages.index');

    Route::post('/child/messages/{thread}', [ChildMessageController::class, 'store'])
        ->name('child.messages.store');

});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

require __DIR__.'/auth.php';
require __DIR__.'/carer.php';
require __DIR__.'/socialworker.php';


