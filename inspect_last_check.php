<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
$last = App\Models\WellbeingCheck::where('young_person_id', $user->id)
    ->whereNotNull('completed_at')
    ->orderByDesc('completed_at')
    ->first();
if ($last) {
    echo 'last check id=' . $last->id . ' completed=' . $last->completed_at . PHP_EOL;
    $ids = Illuminate\Support\Facades\DB::table('check_question_log')
        ->where('wellbeing_check_id', $last->id)
        ->pluck('question_id');
    echo 'questions in last check: ' . count($ids) . PHP_EOL;
} else {
    echo 'no last check' . PHP_EOL;
}
