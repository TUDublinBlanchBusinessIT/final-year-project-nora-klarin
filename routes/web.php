<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CarerDashboardController;

// Child controllers
use App\Http\Controllers\ChildDashboardController;
use App\Http\Controllers\MoodCheckinController;
use App\Http\Controllers\ChildGoalsController;
use App\Http\Controllers\TrustedPeopleController;
use App\Http\Controllers\ChildWeekController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\DiaryEntryController;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\SocialWorkerAppointmentController;



// ✅ Child messages controller (Step 4)
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
    |--------------------------------------------------------------------------
    | Need Help (Support Request)
    |--------------------------------------------------------------------------
    */
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
