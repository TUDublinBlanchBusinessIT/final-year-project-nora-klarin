<?php
use App\Http\Controllers\SocialWorkerDashboardController;
use App\Http\Controllers\SocialWorkerMessagesController;


Route::middleware(['auth', 'role:social_worker'])->group(function () {
    Route::get('/social-worker/dashboard', [SocialWorkerDashboardController::class, 'index'])
        ->name('socialworker.dashboard');

    Route::get('/social-worker/case/{case}', [SocialWorkerDashboardController::class, 'show'])
        ->name('socialworker.case.show');

    Route::get('/social_worker/messages', [SocialWorkerMessagesController::class, 'index'])
        ->name('socialworker.messages.index');

    Route::get('/social_worker/messages/create', [SocialWorkerMessagesController::class, 'create'])
        ->name('socialworker.messages.create');

    Route::post('/social_worker/messages', [SocialWorkerMessagesController::class, 'store'])
        ->name('socialworker.messages.store');
});
