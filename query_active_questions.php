<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$age = 13;
$questions = App\Models\Question::where('is_active', true)
    ->where(function ($q) use ($age) {
        $q->whereNull('age_band_min')
          ->orWhere(function ($q2) use ($age) {
              $q2->where('age_band_min', '<=', $age)
                 ->where('age_band_max', '>=', $age);
          });
    })->get();
echo 'total active questions for age 13: ' . $questions->count() . PHP_EOL;
foreach ($questions->groupBy('domain_id') as $domainId => $group) {
    echo "domain {$domainId}: " . $group->count() . PHP_EOL;
}
