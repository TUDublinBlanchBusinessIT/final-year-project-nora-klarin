<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;

use App\Http\Controllers\SocialWorkerDashboardController;

use App\Http\Controllers\SocialWorkerMessageController;

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

use App\Http\Controllers\WellbeingCheckController;

use App\Http\Controllers\ChildWellbeingController;

use App\Http\Controllers\ChatController;



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

| Shared Auth Routes

|--------------------------------------------------------------------------

*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::patch('/profile/customization', [ProfileController::class, 'updateCustomization'])

        ->name('profile.customization.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');



    Route::get('/wellbeing/{check}/result', [WellbeingCheckController::class, 'result'])

        ->name('wellbeing.result');

});



/*

|--------------------------------------------------------------------------

| Young Person Routes

|--------------------------------------------------------------------------

*/

Route::middleware(['auth', 'role:young_person'])->group(function () {

    Route::get('/child/dashboard', [ChildDashboardController::class, 'index'])

        ->name('child.dashboard');



    Route::post('/child/mood-checkin', [MoodCheckinController::class, 'store'])

        ->name('child.mood.store');



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



    Route::get('/child/support-map', function () {

        return view('child.support-map');

    })->name('child.support.map');



    Route::get('/child/diary', [DiaryEntryController::class, 'index'])

        ->name('child.diary.index');



    Route::post('/child/diary', [DiaryEntryController::class, 'store'])

        ->name('child.diary.store');



    Route::get('/child/messages', [ChildMessageController::class, 'index'])

        ->name('child.messages.index');



    Route::post('/child/messages/{thread?}', [ChildMessageController::class, 'store'])

        ->name('child.messages.store');



    Route::get('/child/wellbeing', [WellbeingCheckController::class, 'create'])

        ->name('child.wellbeing.form');



    Route::post('/child/wellbeing', [WellbeingCheckController::class, 'submit'])

        ->name('child.wellbeing.submit');



    Route::post('/child/wellbeing/store', [ChildWellbeingController::class, 'store'])

        ->name('child.wellbeing.store');



    Route::post('/child/wellbeing/start', [ChildWellbeingController::class, 'start'])

        ->name('child.wellbeing.start');



    Route::post('/child/wellbeing/{check}/submit', [ChildWellbeingController::class, 'submitCheck'])

        ->name('child.wellbeing.submit.check');



    Route::get('/chatbot', [ChatController::class, 'index'])

        ->name('chatbot.index');



    Route::post('/chatbot/send', [ChatController::class, 'send'])

        ->name('chatbot.send');

});



/*

|--------------------------------------------------------------------------

| Social Worker Routes

|--------------------------------------------------------------------------

*/

Route::middleware(['auth', 'role:social_worker'])->group(function () {

    Route::get('/socialworker/dashboard', [SocialWorkerDashboardController::class, 'index'])

        ->name('socialworker.dashboard');



    Route::get('/socialworker/appointments', [SocialWorkerAppointmentController::class, 'index'])

        ->name('socialworker.appointments.index');



    Route::post('/socialworker/appointments', [SocialWorkerAppointmentController::class, 'store'])

        ->name('socialworker.appointments.store');



    Route::get('/socialworker/case-files', [CaseFileController::class, 'index'])

        ->name('socialworker.casefiles.index');



    Route::get('/social-worker/cases', [SocialWorkerDashboardController::class, 'list'])

        ->name('socialworker.case.list');



    Route::get('/social-worker/case/{case}/appointments/create', [SocialWorkerAppointmentController::class, 'create'])

        ->name('social-worker.appointments.create');



    Route::post('/social-worker/case/{case}/appointments', [SocialWorkerAppointmentController::class, 'store'])

        ->name('social-worker.appointments.store');



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



    Route::get('/social-worker/wellbeing-alerts', [WellbeingCheckController::class, 'alerts'])

        ->name('social-worker.wellbeing.alerts');


    Route::get('/social-worker/messages', [SocialWorkerMessageController::class, 'index'])

        ->name('socialworker.messages.index');
    
    Route::post('/social-worker/messages/{thread}', [SocialWorkerMessageController::class, 'store'])

        ->name('socialworker.messages.store');

});



/*

|--------------------------------------------------------------------------

| Admin Routes

|--------------------------------------------------------------------------

*/

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/users', [AdminUserController::class, 'index'])

        ->name('admin.users.index');

});



Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])

    ->name('logout');



require __DIR__ . '/auth.php';

require __DIR__ . '/carer.php';

require __DIR__ . '/socialworker.php';