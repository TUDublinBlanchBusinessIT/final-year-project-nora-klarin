<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$selector = new App\Services\CheckQuestionSelector();
echo (new ReflectionClass($selector))->getFileName() . PHP_EOL;
