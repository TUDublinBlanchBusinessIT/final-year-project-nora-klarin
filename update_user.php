<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::find(6);
$user->dob = '2013-01-01';
$user->save();

echo "Updated user dob\n";
?>