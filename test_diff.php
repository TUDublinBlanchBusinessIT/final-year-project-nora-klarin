<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$dob = Carbon\Carbon::create(2013, 1, 1);
echo 'dob type: ' . get_class($dob) . PHP_EOL;
echo 'date string: ' . $dob->toDateString() . PHP_EOL;
echo 'age property: ' . $dob->age . PHP_EOL;
echo 'diffInYears(now->dob): ' . now()->diffInYears($dob) . PHP_EOL;
echo 'diffInYears(dob->now): ' . $dob->diffInYears(now()) . PHP_EOL;
