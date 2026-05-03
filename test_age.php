<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dob = \Carbon\Carbon::create(2013, 1, 1);
echo 'DOB: ' . $dob->toDateString() . PHP_EOL;
echo 'Now: ' . \Carbon\Carbon::now()->toDateString() . PHP_EOL;
echo 'Age: ' . $dob->age . PHP_EOL;
?>