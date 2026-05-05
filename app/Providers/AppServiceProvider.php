<?php

namespace App\Providers;

use App\Events\WellbeingCheckCompleted;
use App\Listeners\CreateWellbeingAlerts;
use App\Listeners\AnalyzeWellbeingTrend;
use App\Listeners\NotifySocialWorkers;
use App\Listeners\WellbeingCheckCompletedListener;
use App\Models\User;
use App\Observers\YoungPersonObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/dashboard';

    public function register(): void {}

    public function boot(): void
    {
        User::observe(YoungPersonObserver::class);

        Event::listen(WellbeingCheckCompleted::class, CreateWellbeingAlerts::class);
        Event::listen(WellbeingCheckCompleted::class, AnalyzeWellbeingTrend::class);
        Event::listen(WellbeingCheckCompleted::class, NotifySocialWorkers::class);
        Event::listen(WellbeingCheckCompleted::class, WellbeingCheckCompletedListener::class);

        // Commented out until NotifyOverdueWellbeingChecks command is created:
        // Schedule::command(\App\Console\Commands\NotifyOverdueWellbeingChecks::class)
        //     ->dailyAt('08:00')
        //     ->withoutOverlapping();

        View::composer('*', function ($view) {
            if (!auth()->check()) return;

            $user = auth()->user();

            if (in_array($user->role, ['social_worker', 'carer'])) {
                $view->with('notificationCount',
                    $user->unreadNotifications()->count()
                );
                $view->with('notifications',
                    $user->unreadNotifications()->latest()->limit(15)->get()
                );
            }
        });
    }
}
