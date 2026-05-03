<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\SocialWorkerAppointmentController;
use App\Http\Controllers\SocialWorkerMessagesController;
use App\Http\Controllers\WellbeingCheckController;
use App\Http\Controllers\PlacementController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\CaseReportController;


Route::middleware(['auth', 'role:social_worker'])->prefix('social-worker')->name('socialworker.')->group(function () {
    Route::get('cases/{case}/report', [CaseReportController::class, 'show'])
        ->name('cases.report');
        
    Route::get('/dashboard', [SocialWorkerDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/cases', [CaseFileController::class, 'index'])
        ->name('cases.index');


    Route::get('/cases/{case}', [CaseFileController::class, 'show'])
        ->name('cases.show');

    Route::get('/cases/{case}/edit', [CaseFileController::class, 'edit'])
        ->name('cases.edit');

    Route::put('/cases/{case}', [CaseFileController::class, 'update'])
        ->name('cases.update');

    Route::post('/cases/{case}/assign-carer',  [CaseFileController::class, 'assignCarer'])  ->name('cases.assignCarer');
    Route::post('/cases/{case}/placements',    [CaseFileController::class, 'storePlacement'])->name('cases.placements.store');
    Route::post('/cases/{case}/medical',       [CaseFileController::class, 'storeMedical'])  ->name('cases.medical.store');
    Route::post('/cases/{case}/education',     [CaseFileController::class, 'storeEducation'])->name('cases.education.store');
    Route::post('/cases/{case}/documents',     [CaseFileController::class, 'storeDocument']) ->name('cases.documents.store');

    Route::get('/appointments',         [SocialWorkerAppointmentController::class, 'index']) ->name('appointments.index');
    Route::get('/cases/{case}/appointments/create', [SocialWorkerAppointmentController::class, 'create'])
        ->name('appointments.create');
    Route::post('/appointments',        [SocialWorkerAppointmentController::class, 'store']) ->name('appointments.store');

    Route::get('/messages',         [SocialWorkerMessagesController::class, 'index']) ->name('messages.index');
    Route::get('/messages/create',  [SocialWorkerMessagesController::class, 'create'])->name('messages.create');
    Route::post('/messages',        [SocialWorkerMessagesController::class, 'store']) ->name('messages.store');
    Route::get('/messages/{partner}', [SocialWorkerMessagesController::class, 'show'])->name('messages.show');
    Route::get('/placements', [PlacementController::class, 'map'])
        ->name('placements.map');

    // Wellbeing check details
    Route::get('/wellbeing-check/{check}/details', [WellbeingCheckController::class, 'getDetails'])
        ->name('wellbeing.details');

    Route::get('/wellbeing-alerts', [WellbeingCheckController::class, 'alerts'])
        ->name('wellbeing.alerts');

    Route::patch('/cases/{case}/mark-reviewed', [CaseFileController::class, 'markReviewed'])
    ->name('cases.markReviewed');
 
Route::patch('/alerts/{alert}/dismiss', [AlertController::class, 'dismiss'])
    ->name('alerts.dismiss');

    Route::patch('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])
    ->name('alerts.acknowledge');

    Route::get('/cases/{case}/goals', [GoalController::class, 'index'])
        ->name('goals.index');

    Route::post('/cases/{case}/goals', [GoalController::class, 'store'])
        ->name('goals.store');

    Route::post('/goals/{caseGoalId}/approve', [GoalController::class, 'approve'])
        ->name('goals.approve');

    Route::post('/goals/{caseGoalId}/tasks', [GoalController::class, 'addTask'])
        ->name('goals.tasks.store');

    Route::post('/goals/{caseGoalId}/complete', [GoalController::class, 'complete'])
        ->name('goals.complete');

    Route::delete('/goals/{caseGoalId}', [GoalController::class, 'dismiss'])
    ->name('goals.dismiss');

    Route::post('goals/tasks/{task}/publish', [GoalController::class, 'publishTask'])
    ->name('goals.tasks.publish');

    Route::delete('goals/tasks/{task}', [GoalController::class, 'deleteTask'])
    ->name('goals.tasks.delete');

    Route::post('tasks/{task}/complete', [GoalController::class, 'completeTask'])
    ->name('tasks.complete');

    Route::post('tasks/{task}/uncomplete', [GoalController::class, 'uncompleteTask'])
    ->name('tasks.uncomplete');

    Route::get('cases/{case}/report', [CaseReportController::class, 'show'])
        ->name('cases.report');

    Route::post('goals/{caseGoalId}/approve', [GoalController::class, 'approve'])
        ->name('goals.approve');
    });