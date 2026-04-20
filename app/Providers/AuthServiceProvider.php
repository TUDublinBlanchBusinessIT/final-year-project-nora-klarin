<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;


class AuthServiceProvider extends ServiceProvider
{

protected $policies = [
    WellbeingCheck::class => WellbeingPolicy::class,
    Alert::class          => WellbeingPolicy::class,
];
}