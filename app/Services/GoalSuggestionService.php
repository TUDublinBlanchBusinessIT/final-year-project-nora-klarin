<?php
namespace App\Services;

use App\Models\WellbeingCheck;
use App\Models\CaseFile;
use Illuminate\Support\Facades\DB;

class GoalSuggestionService
{
    public function suggestFromCheck(WellbeingCheck $check): int
    {
        $caseFile = CaseFile::where('young_person_id', $check->young_person_id)
            ->where('status', 'open')
            ->latest()
            ->first();

        if (!$caseFile) return 0;

        // Get average normalised score per tag across this check's responses
        $tagScores = DB::table('wellbeing_responses')
            ->join('question_tag', 'wellbeing_responses.question_id', '=', 'question_tag.question_id')
            ->where('wellbeing_responses.wellbeing_check_id', $check->id)
            ->select(
                'question_tag.tag_id',
                DB::raw('AVG(wellbeing_responses.normalised_score) as avg_score')
            )
            ->groupBy('question_tag.tag_id')
            ->get();

        $suggested = 0;

        foreach ($tagScores as $row) {
            // Find templates where this tag's score is below the trigger threshold
            $templates = DB::table('tag_goal_templates')
                ->where('tag_id', $row->tag_id)
                ->where('trigger_threshold', '>=', $row->avg_score)
                ->pluck('goal_template_id');

            foreach ($templates as $templateId) {
                if ($this->createSuggestion($templateId, $caseFile->id, $check->id)) {
                    $suggested++;
                }
            }
        }

        return $suggested;
    }

    private function createSuggestion(int $templateId, int $caseFileId, int $checkId): bool
    {
        $template = DB::table('goal_templates')->find($templateId);
        if (!$template) return false;

        // Don't duplicate — skip if there's already a pending/in_progress goal
        // from this same template on this case
        $exists = DB::table('case_goals')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->where('case_goals.case_file_id', $caseFileId)
            ->where('goals.template_id', $templateId)
            ->whereIn('case_goals.status', ['pending', 'in_progress'])
            ->exists();

        if ($exists) return false;

        $goalId = DB::table('goals')->insertGetId([
            'title'            => $template->title,
            'description'      => $template->description,
            'template_id'      => $templateId,
            'source_domain_id' => $template->source_domain_id,
            'suggested_at'     => now(),
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('case_goals')->insert([
            'case_file_id'    => $caseFileId,
            'goal_id'         => $goalId,
            'status'          => 'pending',
            'source_check_id' => $checkId,
            'child_visible'   => 0,   // social worker approves before child sees it
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return true;
    }
}