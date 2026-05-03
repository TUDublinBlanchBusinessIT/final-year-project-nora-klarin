<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Questions with age ranges:\n";
$questions = App\Models\Question::select('id', 'text', 'min_age', 'max_age')->take(10)->get();
foreach ($questions as $q) {
    echo $q->id . ': ' . substr($q->text, 0, 50) . ' (min: ' . $q->min_age . ', max: ' . $q->max_age . ')' . PHP_EOL;
}
?>