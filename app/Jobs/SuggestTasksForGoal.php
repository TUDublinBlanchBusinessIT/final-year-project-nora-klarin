<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuggestTasksForGoal implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $caseGoalId,
        public readonly int $goalId,
    ) {}

public function handle(): void
{
    $goal = DB::table('goals')
        ->leftJoin('domains', 'goals.source_domain_id', '=', 'domains.id')
        ->where('goals.id', $this->goalId)
        ->select('goals.*', 'domains.name as domain_name')
        ->first();

    if (!$goal) return;

    $youngPerson = DB::table('case_goals')
        ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
        ->join('users', 'case_files.young_person_id', '=', 'users.id')
        ->where('case_goals.id', $this->caseGoalId)
        ->select('users.dob')
        ->first();

    $age = $youngPerson?->dob
        ? \Carbon\Carbon::parse($youngPerson->dob)->age
        : 14;

    $ageGuide = match(true) {
        $age <= 10 => 'aged 8–10 (use very simple words, short sentences, fun language)',
        $age <= 13 => 'aged 11–13 (friendly, straightforward language)',
        $age <= 16 => 'aged 14–16 (teen-appropriate, respectful, not patronising)',
        default    => 'aged 17+ (near-adult language, treat them as capable)',
    };

    $prompt = <<<PROMPT
You are helping a young person in foster care work on a personal goal.
The young person is {$ageGuide}.

Goal title: "{$goal->title}"
Goal description: "{$goal->description}"
Domain: "{$goal->domain_name}"

Generate exactly 3 concrete, small, achievable tasks written directly FOR the child in first-person language (e.g. "I will...", "Try...", "Ask...").

Rules:
- Tasks should be things the child can actually DO themselves this week
- Keep language simple, warm and encouraging
- Each task should take no more than 30 minutes
- Do NOT use clinical or professional language
- Do NOT just restate the goal

Return ONLY a valid JSON array with no preamble, no markdown, no explanation:
[{"title": "...", "description": "..."}, ...]
PROMPT;

$response = \Illuminate\Support\Facades\Http::withoutVerifying()
    ->withHeaders([
        'Content-Type' => 'application/json',
    ])->timeout(30)->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . config('services.gemini.key'),
        [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            'generationConfig' => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 400,
            ],
        ]
    );

    if (!$response->successful()) {
        \Illuminate\Support\Facades\Log::warning('SuggestTasksForGoal: Gemini API call failed', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);
        return;
    }

    $text = $response->json('candidates.0.content.parts.0.text', '');

    // Strip any accidental markdown fences
    $text  = preg_replace('/```json|```/', '', $text);
    $tasks = json_decode(trim($text), true);

    if (!is_array($tasks)) {
        \Illuminate\Support\Facades\Log::warning('SuggestTasksForGoal: could not parse JSON', ['raw' => $text]);
        return;
    }

    foreach ($tasks as $task) {
        if (empty($task['title'])) continue;

        $taskId = DB::table('tasks')->insertGetId([
            'title'         => $task['title'],
            'description'   => $task['description'] ?? null,
            'case_goal_id'  => $this->caseGoalId,
            'ai_suggested'  => true,
            'child_visible' => false,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('task_goal')->insert([
            'goal_id'    => $this->goalId,
            'task_id'    => $taskId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
}