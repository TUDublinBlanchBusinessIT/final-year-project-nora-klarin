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
    }
}
