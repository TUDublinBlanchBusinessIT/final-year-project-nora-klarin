<?php
class SuggestTasksForGoal
{
public function handle()
{
    $goal = DB::table('goals')->find($this->goalId);

    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'x-api-key'         => config('services.anthropic.key'),
        'anthropic-version' => '2023-06-01',
        'content-type'      => 'application/json',
    ])->post('https://api.anthropic.com/v1/messages', [
        'model'      => 'claude-sonnet-4-20250514',
        'max_tokens' => 300,
        'messages'   => [[
            'role'    => 'user',
            'content' => "Generate 3 short, practical tasks for a child in foster care working on this goal: \"{$goal->title}\". Return only a JSON array of objects with 'title' and 'description' keys. No preamble.",
        ]],
    ]);

    $tasks = json_decode($response->json('content.0.text'), true);

    if (!is_array($tasks)) return;

    foreach ($tasks as $task) {
        $taskId = DB::table('tasks')->insertGetId([
            'title'        => $task['title'],
            'description'  => $task['description'],
            'case_goal_id' => $this->caseGoalId,
            'created_at'   => now(),
            'updated_at'   => now(),
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