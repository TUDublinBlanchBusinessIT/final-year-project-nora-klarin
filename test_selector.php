<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
$selector = new App\Services\CheckQuestionSelector();
$questions = $selector->selectFor($user);
echo 'selected class: ' . get_class($questions) . PHP_EOL;
echo 'selected count: ' . (is_object($questions) && method_exists($questions, 'count') ? $questions->count() : 'no count') . PHP_EOL;
var_dump($questions);
