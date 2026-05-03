<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$age = 13;
$domainIds = Illuminate\Support\Facades\DB::table('domains')->pluck('id');
$safetyId = Illuminate\Support\Facades\DB::table('domains')->where('name', 'Safety')->value('id');
echo 'safetyId=' . $safetyId . PHP_EOL;
foreach ($domainIds as $domainId) {
    $cands = App\Models\Question::where('domain_id', $domainId)
        ->where('is_active', true)
        ->where(function ($q) use ($age) {
            $q->whereNull('age_band_min')
              ->orWhere(function ($q2) use ($age) {
                  $q2->where('age_band_min', '<=', $age)
                     ->where('age_band_max', '>=', $age);
              });
        })->count();
    echo "domain {$domainId} candidates={$cands}" . PHP_EOL;
}
