<?php

namespace App\Providers;

use App\Events\WellbeingCheckCompleted;
use App\Listeners\CreateWellbeingAlerts;
use App\Listeners\AnalyzeWellbeingTrend;
use App\Listeners\NotifySocialWorkers;
use App\Models\User;
use App\Observers\YoungPersonObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public const HOME = '/dashboard';

    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(YoungPersonObserver::class);

        Event::listen(WellbeingCheckCompleted::class, CreateWellbeingAlerts::class);
        Event::listen(WellbeingCheckCompleted::class, AnalyzeWellbeingTrend::class);
        Event::listen(WellbeingCheckCompleted::class, NotifySocialWorkers::class);
        Event::listen(WellbeingCheckCompleted::class,WellbeingCheckCompletedListener::class,
        );
        
        Schedule::command(NotifyOverdueWellbeingChecks::class)
            ->dailyAt('08:00')
            ->withoutOverlapping();
            
                View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->role === 'social_worker') {
                $user = auth()->user();
 
                $view->with('notificationCount',
                    $user->unreadNotifications()->count()
                );
 
                $view->with('notifications',
                    $user->unreadNotifications()
                        ->latest()
                        ->limit(15)
                        ->get()
                );
            }
        });

    }

}
