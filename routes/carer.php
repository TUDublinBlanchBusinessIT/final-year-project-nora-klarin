<?php



use App\Http\Controllers\CarerDashboardController;

use App\Http\Controllers\CarerCalendarController;

use App\Http\Controllers\CarerMessageController;

use App\Http\Controllers\CarerDocumentController;

use App\Http\Controllers\CarerCaseFileController;

use Illuminate\Support\Facades\Route;



Route::middleware(['auth', 'role:carer'])->group(function () {



    Route::get('/carer/dashboard', [CarerDashboardController::class, 'index'])

        ->name('carer.dashboard');



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



    Route::post('/carer/documents', [CarerDocumentController::class, 'store'])

        ->name('carer.documents.store');



    Route::get('/carer/documents/{doc}/download', [CarerDocumentController::class, 'download'])

        ->name('carer.documents.download');



    Route::delete('/carer/documents/{doc}', [CarerDocumentController::class, 'destroy'])

        ->name('carer.documents.destroy');


    Route::get('/carer/case-file', [CarerCaseFileController::class, 'show'])

    ->name('carer.case-file.show');

});

