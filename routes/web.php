<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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
use App\Http\Controllers\ChildMessageController;
use App\Http\Controllers\WellbeingCheckController;


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

    Route::get('/child/wellbeing/check', [WellbeingCheckController::class, 'index'])
        ->name('child.wellbeing.check');

    Route::post('/child/wellbeing/start', [WellbeingCheckController::class, 'start'])
        ->name('child.wellbeing.start');

    Route::post('/child/wellbeing/{check}/submit', [WellbeingCheckController::class, 'submitCheck'])
        ->name('child.wellbeing.submit');

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

        Route::get('/social-worker/wellbeing-alerts', [WellbeingCheckController::class, 'alerts'])
        ->name('social-worker.wellbeing.alerts');
        
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

    Route::post('/child/diary', [DiaryEntryController::class, 'store'])
        ->name('child.diary.store');

    
    Route::get('/child/messages', [ChildMessageController::class, 'index'])
        ->name('child.messages.index');

    Route::post('/child/messages/{thread}', [ChildMessageController::class, 'store'])
        ->name('child.messages.store');

        //Route::get('/child/wellbeing', [WellbeingCheckController::class, 'create'])
        //->name('child.wellbeing.form');

    //Route::post('/child/wellbeing', [WellbeingCheckController::class, 'submit'])
        //->name('child.wellbeing.submit');

    //Route::get('/child/wellbeing/{check}/result', [WellbeingCheckController::class, 'result'])
        //->name('child.wellbeing.result');

});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::middleware(['auth', 'role:child'])->group(function() {
    Route::post('/child/wellbeing', [\App\Http\Controllers\ChildWellbeingController::class, 'store'])->name('child.wellbeing.store');
});

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

    Route::get('/child/support', [SupportRequestController::class, 'index'])
        ->name('child.support');

    Route::post('/child/support', [SupportRequestController::class, 'store'])
        ->name('child.support.store');

    
    Route::post('/child/diary', [DiaryEntryController::class, 'store'])
        ->name('child.diary.store');

  
    Route::get('/child/messages', [ChildMessageController::class, 'index'])
        ->name('child.messages.index');

    Route::post('/child/messages/{thread}', [ChildMessageController::class, 'store'])
        ->name('child.messages.store');

        //Route::post('/wellbeing/start', [WellbeingCheckController::class, 'start'])
        //->middleware('role:young_person')
        //->name('wellbeing.start');

    //Route::post('/wellbeing/{check}/submit', [WellbeingCheckController::class, 'submit'])
        //->middleware('role:young_person')
        //->name('wellbeing.submit');

    Route::get('/wellbeing/{youngPerson}/history', [WellbeingCheckController::class, 'history'])
        ->middleware('role:staff')
        ->name('wellbeing.history');

    Route::get('/wellbeing/{youngPerson}/goal-suggestions', [WellbeingCheckController::class, 'goalSuggestions'])
        ->middleware('role:social_worker')
        ->name('wellbeing.goal-suggestions');

    Route::get('/alerts/unacknowledged', [AlertController::class, 'unacknowledged'])
        ->middleware('role:staff')
        ->name('alerts.unacknowledged');

    Route::get('/alerts/{youngPerson}', [AlertController::class, 'forYoungPerson'])
        ->middleware('role:staff')
        ->name('alerts.for-young-person');

    Route::patch('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])
        ->middleware('role:staff')
        ->name('alerts.acknowledge');

    Route::patch('/alerts/acknowledge-all', [AlertController::class, 'acknowledgeAll'])
        ->middleware('role:staff')
        ->name('alerts.acknowledge-all');
});


Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout');

Route::get('/child/wellbeing/check', [WellbeingCheckController::class, 'show'])
    ->middleware('auth')
    ->name('wellbeing.check');

Route::prefix('demo')->name('demo.')->group(function () {

    Route::get('/', function () {
        $accounts = \App\Models\User::whereIn('role', ['young_person', 'social_worker', 'carer'])
            ->orderBy('role')->orderBy('name')
            ->get(['id', 'name', 'role', 'email']);
        return view('demo.switcher', compact('accounts'));
    })->name('switcher');

    Route::post('/login-as/{user}', function (\App\Models\User $user) {
        Auth::login($user);
        return match($user->role) {
            'young_person'  => redirect()->route('child.wellbeing.check'),
            default         => redirect()->route('dashboard'),
        };
    })->name('login-as');

});

require __DIR__.'/auth.php';
require __DIR__.'/carer.php';
require __DIR__.'/socialworker.php';



