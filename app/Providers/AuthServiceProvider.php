<?php

namespace App\Providers;
use App\Policies\WellbeingPolicy;
use App\Models\WellbeingCheck;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Alert;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{

protected $policies = [
    WellbeingCheck::class => WellbeingPolicy::class,
    Alert::class          => WellbeingPolicy::class,
];

public function boot()
{
    $this->registerPolicies();

    Gate::define('startWellbeingCheck', function ($user, $youngPerson) {
        \Log::info('startWellbeingCheck gate called', [
            'user_id' => $user?->id,
            'user_role' => $user?->role,
            'youngPerson_id' => $youngPerson?->id,
            'youngPerson_role' => $youngPerson?->role,
            'youngPerson_age' => $youngPerson?->age(),
            'youngPerson_dob' => $youngPerson?->dob,
        ]);
        $policy = app(WellbeingPolicy::class);
        $result = $policy->startWellbeingCheck($user, $youngPerson);
        \Log::info('startWellbeingCheck policy result: ' . ($result ? 'ALLOWED' : 'DENIED'));
        return $result;
    });

    Gate::define('submitWellbeingCheck', function ($user, $check) {
        $policy = app(WellbeingPolicy::class);
        return $policy->submitWellbeingCheck($user, $check);
    });

    Gate::define('viewWellbeingHistory', function ($user, $youngPerson) {
        $policy = app(WellbeingPolicy::class);
        return $policy->viewWellbeingHistory($user, $youngPerson);
    });

    Gate::define('viewAlerts', function ($user) {
        $policy = app(WellbeingPolicy::class);
        return $policy->viewAlerts($user);
    });

    Gate::define('acknowledgeAlert', function ($user, $alert) {
        $policy = app(WellbeingPolicy::class);
        return $policy->acknowledgeAlert($user, $alert);
    });
}

}