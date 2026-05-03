<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
$selector = new App\Services\CheckQuestionSelector();
$reflection = new ReflectionClass($selector);
$method = $reflection->getMethod('getCandidates');
$method->setAccessible(true);
$age = 13;
$excludedIds = collect();
$domainIds = Illuminate\Support\Facades\DB::table('domains')->pluck('id');
foreach ($domainIds as $domainId) {
    $cands = $method->invoke($selector, $domainId, $excludedIds, $age);
    echo "domain {$domainId} result count=" . $cands->count() . PHP_EOL;
}
