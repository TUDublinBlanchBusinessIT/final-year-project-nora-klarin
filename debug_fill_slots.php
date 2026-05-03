<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$user = App\Models\User::find(6);
$selector = new App\Services\CheckQuestionSelector();
$reflection = new ReflectionClass($selector);
$methodBuild = $reflection->getMethod('buildSlotMap');
$methodBuild->setAccessible(true);
$methodCandidates = $reflection->getMethod('getCandidates');
$methodCandidates->setAccessible(true);
$methodTagRecurrence = $reflection->getMethod('getTagRecurrence');
$methodTagRecurrence->setAccessible(true);
$lastCheck = App\Models\WellbeingCheck::where('young_person_id', $user->id)
    ->whereNotNull('completed_at')
    ->orderByDesc('completed_at')
    ->first();
$domainIds = Illuminate\Support\Facades\DB::table('domains')->pluck('id');
$safetyId = Illuminate\Support\Facades\DB::table('domains')->where('name','Safety')->value('id');
$excludedMethod = $reflection->getMethod('getRecentlyUsedQuestionIds');
$excludedMethod->setAccessible(true);
$excludedIds = $excludedMethod->invoke($selector, $user);
$slotMap = $methodBuild->invoke($selector, $user, $lastCheck, $domainIds, $safetyId);
echo 'excluded count=' . $excludedIds->count() . PHP_EOL;
echo 'excluded ids=' . $excludedIds->implode(',') . PHP_EOL;
echo "slotMap=" . json_encode($slotMap) . PHP_EOL;
$excludedIds = collect();
$age = 13;
$selected = collect();
$tagRecurrence = $methodTagRecurrence->invoke($selector, null);
foreach ($slotMap as $domainId => $slotCount) {
    $candidates = $methodCandidates->invoke($selector, $domainId, $excludedIds, $age);
    echo "domain {$domainId} candidates=" . $candidates->count() . " slotCount={$slotCount}\n";
    if ($candidates->isEmpty()) {
        $candidates = $methodCandidates->invoke($selector, $domainId, collect(), $age);
        echo "fallback candidates=" . $candidates->count() . "\n";
    }
    if ($candidates->isEmpty()) {
        echo "no candidates after fallback for domain {$domainId}\n";
        continue;
    }
    $scored = $candidates->map(function ($question) use ($tagRecurrence) {
        $tagIds = Illuminate\Support\Facades\DB::table('question_tag')->where('question_id', $question->id)->pluck('tag_id');
        $recurrenceScore = $tagIds->sum(fn($tid) => $tagRecurrence->get($tid, 0));
        $question->recurrence_score = $recurrenceScore;
        return $question;
    });
    $sorted = $scored->sortByDesc('recurrence_score')->values();
    $pick = $sorted->groupBy('recurrence_score')->map(fn($group) => $group->shuffle())->flatten()->take($slotCount);
    echo "picked=" . $pick->count() . "\n";
    $selected = $selected->merge($pick);
    $excludedIds = $excludedIds->merge($pick->pluck('id'));
}
echo "selected total=" . $selected->count() . PHP_EOL;
foreach ($selected as $q) {
    echo $q->id . ' domain ' . $q->domain_id . ' score=' . $q->recurrence_score . PHP_EOL;
}
$methodResolve = $reflection->getMethod('resolveWordings');
$methodResolve->setAccessible(true);
$resolved = $methodResolve->invoke($selector, $selected, $age);
echo 'resolved count=' . $resolved->count() . PHP_EOL;
foreach ($resolved as $q) {
    echo $q->id . ' text=' . substr($q->text,0,30) . PHP_EOL;
}
