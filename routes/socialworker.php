<?php
use App\Http\Controllers\SocialWorkerDashboardController;

Route::middleware(['auth', 'role:social_worker'])->group(function () {
    Route::get('/social-worker/dashboard', [SocialWorkerDashboardController::class, 'index'])
        ->name('socialworker.dashboard');

    Route::get('/social-worker/case/{case}', [SocialWorkerDashboardController::class, 'show'])
        ->name('socialworker.case.show');
});
