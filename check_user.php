<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::find(6);
echo 'Age: ' . $user->age() . PHP_EOL;
var_dump($user->dob);
?>