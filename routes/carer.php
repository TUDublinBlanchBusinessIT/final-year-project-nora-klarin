<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarerDashboardController;
use App\Http\Controllers\CarerCalendarController;

use App\Http\Controllers\CarerMessageController;

use App\Http\Controllers\CarerDocumentController;
use App\Http\Controllers\CarerCaseFileController;
use App\Http\Controllers\WellbeingController;
use App\Http\Controllers\WellbeingCheckController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CarerProxyWellbeingController;

Route::middleware(['auth', 'role:carer'])->group(function () {
    Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])
        ->name('carer.dashboard');

    Route::get('/carer/cases/{case}/wellbeing/proxy',
    [\App\Http\Controllers\CarerProxyWellbeingController::class, 'create'])
    ->name('carer.proxy.wellbeing.create');

    Route::post('/carer/proxy-wellbeing',
    [\App\Http\Controllers\CarerProxyWellbeingController::class, 'store'])
    ->name('carer.proxy.wellbeing.store');

    Route::get('/carer/calendar', [CarerCalendarController::class, 'index'])
        ->name('carer.calendar');

    Route::post('/carer/calendar', [CarerCalendarController::class, 'store'])
        ->name('carer.calendar.store');

    Route::get('/carer/messages', [CarerMessageController::class, 'index'])
        ->name('carer.messages.index');

    Route::get('/carer/messages/create', [CarerMessageController::class, 'create'])
        ->name('carer.messages.create');

    Route::post('/carer/messages', [CarerMessageController::class, 'store'])
        ->name('carer.messages.store');

    Route::get('/carer/documents', [CarerDocumentController::class, 'index'])
        ->name('carer.documents.index');

    Route::post('/carer/cases/{case}/documents', [CarerCaseFileController::class, 'storeDocument'])
    ->name('carer.cases.documents.store');

    Route::get('/carer/documents/{doc}/download', [CarerDocumentController::class, 'download'])
        ->name('carer.documents.download');

    Route::delete('/carer/documents/{doc}', [CarerDocumentController::class, 'destroy'])
        ->name('carer.documents.destroy');

    Route::get('/carer/cases', [CarerCaseFileController::class, 'index'])->name('carer.cases.index');
    Route::get('/carer/cases/{case}', [CarerCaseFileController::class, 'show'])->name('carer.cases.show');


    Route::post('/wellbeing', [WellbeingController::class, 'store'])
        ->name('carer.wellbeing.store');

    Route::post('/carer/young-person/{youngPerson}/wellbeing/start', [WellbeingCheckController::class, 'startForYoungPerson'])
        ->name('carer.wellbeing.start');

    Route::post('/carer/wellbeing/{check}/submit', [WellbeingCheckController::class, 'submitCheck'])
        ->name('carer.wellbeing.submit');

    Route::post('/carer/documents', [CarerDocumentController::class, 'store'])
        ->name('carer.documents.store');

    Route::post('/carer/cases/{case}/documents', [CarerCaseFileController::class, 'storeDocument'])
        ->name('carer.cases.documents.store');

    Route::patch('/carer/notifications/{id}/read',
    [\App\Http\Controllers\NotificationController::class, 'markRead'])
    ->name('carer.notifications.markRead');

Route::post('/carer/notifications/mark-all-read',
    [\App\Http\Controllers\NotificationController::class, 'markAllRead'])
    ->name('carer.notifications.markAllRead');
});
