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
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\SocialWorkerAppointmentController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;
use App\Http\Controllers\ChildWeekController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\ChildMessageController;
use App\Http\Controllers\SocialWorkerMessagesController;
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\WellbeingCheckController;
use App\Http\Controllers\ChildWellbeingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;

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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/customization', [ProfileController::class, 'updateCustomization'])
        ->name('profile.customization.update');

});

    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('socialworker.notifications.markRead');
Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('socialworker.notifications.markAllRead');
Route::middleware(['auth', 'role:young_person'])->group(function () {
    Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])
        ->name('child.dashboard');

    Route::get('/child/wellbeing', [WellbeingCheckController::class, 'create'])
        ->name('child.wellbeing.form');

    Route::get('/child/wellbeing/check', [WellbeingCheckController::class, 'index'])
        ->name('child.wellbeing.check');

    Route::post('/child/wellbeing/start', [WellbeingCheckController::class, 'start'])
        ->name('child.wellbeing.start');

    Route::post('/child/wellbeing/{check}/submit', [WellbeingCheckController::class, 'submitCheck'])
        ->name('child.wellbeing.submit');

    Route::post('/child/mood-checkin', [MoodCheckinController::class, 'store'])
        ->name('child.mood.store');

    Route::get('/child/mood/{mood}', [MoodCheckinController::class, 'store'])
        ->name('child.mood.save');
    Route::get('/child/goals', [ChildGoalsController::class, 'index'])->name('child.goals');

    Route::post('/child/goals/{caseGoalId}/accept', [ChildGoalsController::class, 'accept'])->name('child.goals.accept');

    Route::post('/child/tasks/{taskId}/complete', [ChildGoalsController::class, 'completeTask'])->name('child.tasks.complete');
    
    Route::post('/child/tasks/{taskId}/uncomplete', [ChildGoalsController::class, 'uncompleteTask'])->name('child.tasks.uncomplete');
    Route::get('/child/goals', [ChildGoalsController::class, 'index'])
        ->name('child.goals');

    Route::post('/child/goals', [ChildGoalsController::class, 'store'])
        ->name('child.goals.store');

    Route::post('/support/request', [TrustedPeopleController::class, 'requestSupport'])->name('child.support.request');

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

    Route::get('/child/diary', [DiaryEntryController::class, 'index'])
        ->name('child.diary.index');

    Route::post('/child/diary', [DiaryEntryController::class, 'store'])
        ->name('child.diary.store');

    Route::get('/child/messages', [ChildMessageController::class, 'index'])
        ->name('child.messages.index');

    Route::post('/child/messages/{thread?}', [ChildMessageController::class, 'store'])
        ->name('child.messages.store');

    Route::get('/child/wellbeing/{check}/result', [WellbeingCheckController::class, 'result'])
        ->name('child.wellbeing.result');

    Route::post('/child/wellbeing/store', [ChildWellbeingController::class, 'store'])
        ->name('child.wellbeing.store');

    Route::get('/wellbeing/{youngPerson}/history', [WellbeingCheckController::class, 'history'])
        ->middleware('role:social_worker')
        ->name('wellbeing.history');

    Route::get('/wellbeing/{youngPerson}/goal-suggestions', [WellbeingCheckController::class, 'goalSuggestions'])
        ->middleware('role:social_worker')
        ->name('wellbeing.goal-suggestions');

    Route::get('/child/support-map', function () {
        return view('child.support-map');
        })->name('child.support.map');

    Route::get('/chatbot', [ChatController::class, 'index'])
        ->name('chatbot.index');

    Route::post('/chatbot/send', [ChatController::class, 'send'])
        ->name('chatbot.send');

    Route::post('/goals/tasks/{task}/complete',   [ChildGoalsController::class, 'completeTask'])->name('child.goals.tasks.complete');
Route::post('/goals/tasks/{task}/uncomplete', [ChildGoalsController::class, 'uncompleteTask'])->name('child.goals.tasks.uncomplete');

});


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [AdminUserController::class, 'editUser'])->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [AdminUserController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroyUser'])->name('admin.users.destroy');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])

    ->name('logout');


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