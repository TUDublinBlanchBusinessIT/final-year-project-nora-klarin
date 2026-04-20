<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\CaseFileController;
use App\Http\Controllers\SocialWorkerAppointmentController;
use App\Http\Controllers\SocialWorkerMessagesController;
use App\Http\Controllers\WellbeingCheckController;
use App\Http\Controllers\PlacementController;


Route::middleware(['auth', 'role:social_worker'])->prefix('social-worker')->name('socialworker.')->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────────
    Route::get('/dashboard', [SocialWorkerDashboardController::class, 'index'])
        ->name('dashboard');

    // ── Cases ──────────────────────────────────────────────────────────────────
    Route::get('/cases', [CaseFileController::class, 'index'])
        ->name('cases.index');


    Route::get('/cases/{case}', [CaseFileController::class, 'show'])
        ->name('cases.show');


    Route::get('/cases/{case}/edit', [CaseFileController::class, 'edit'])
        ->name('cases.edit');

    Route::put('/cases/{case}', [CaseFileController::class, 'update'])
        ->name('cases.update');

    // Case sub-resources
    Route::post('/cases/{case}/assign-carer',  [CaseFileController::class, 'assignCarer'])  ->name('cases.assignCarer');
    Route::post('/cases/{case}/placements',    [CaseFileController::class, 'storePlacement'])->name('cases.placements.store');
    Route::post('/cases/{case}/medical',       [CaseFileController::class, 'storeMedical'])  ->name('cases.medical.store');
    Route::post('/cases/{case}/education',     [CaseFileController::class, 'storeEducation'])->name('cases.education.store');
    Route::post('/cases/{case}/documents',     [CaseFileController::class, 'storeDocument']) ->name('cases.documents.store');

    // ── Appointments ───────────────────────────────────────────────────────────
    Route::get('/appointments',         [SocialWorkerAppointmentController::class, 'index']) ->name('appointments.index');
    Route::get('/cases/{case}/appointments/create', [SocialWorkerAppointmentController::class, 'create'])
        ->name('appointments.create');
    Route::post('/appointments',        [SocialWorkerAppointmentController::class, 'store']) ->name('appointments.store');

    // ── Messages ───────────────────────────────────────────────────────────────
    Route::get('/messages',         [SocialWorkerMessagesController::class, 'index']) ->name('messages.index');
    Route::get('/messages/create',  [SocialWorkerMessagesController::class, 'create'])->name('messages.create');
    Route::post('/messages',        [SocialWorkerMessagesController::class, 'store']) ->name('messages.store');
    Route::get('/messages/{partner}', [SocialWorkerMessagesController::class, 'show'])->name('messages.show');
    Route::get('/placements', [PlacementController::class, 'map'])
    ->name('placements.map');
//    Route::get('/placements', fn () => view('socialworker.placements.map'))
    //    ->name('placements.map');

    // ── Reports ────────────────────────────────────────────────────────────────
   // Route::get('/reports', fn () => view('socialworker.reports.index'))
   //     ->name('reports.index');

    // ── Wellbeing ──────────────────────────────────────────────────────────────
    Route::get('/wellbeing-alerts', [WellbeingCheckController::class, 'alerts'])
        ->name('wellbeing.alerts');

});