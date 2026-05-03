<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
$selector = new App\Services\CheckQuestionSelector();
$reflection = new ReflectionClass($selector);
$method = $reflection->getMethod('buildSlotMap');
$method->setAccessible(true);
$domainIds = Illuminate\Support\Facades\DB::table('domains')->pluck('id');
$safetyId = Illuminate\Support\Facades\DB::table('domains')->where('name','Safety')->value('id');
$slotMap = $method->invoke($selector, $user, null, $domainIds, $safetyId);
echo 'slotMap: ' . json_encode($slotMap) . PHP_EOL;
