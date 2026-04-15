<?php



namespace App\Services;



use App\Models\Question;

use App\Models\WellbeingCheck;



class CheckQuestionSelector

{

    public function selectForCase(int $caseFileId, int $limit = 8)

    {

        $lastCheck = WellbeingCheck::with('domainScores')

            ->where('case_file_id', $caseFileId)

            ->orderByDesc('created_at')

            ->first();



        // First ever check: balanced starter set

        if (!$lastCheck || $lastCheck->domainScores->isEmpty()) {

            return Question::with('domain')

                ->where('is_active', true)

                ->get()

                ->groupBy('domain_id')

                ->flatMap(function ($questions) {

                    return $questions->take(2);

                })

                ->take($limit)

                ->values();

        }



        // Adaptive check: weakest domains first

        $lowestDomains = $lastCheck->domainScores

            ->sortBy('average_score')

            ->pluck('domain_id')

            ->take(4);



        $selected = collect();



        foreach ($lowestDomains as $domainId) {

            $domainQuestions = Question::with('domain')

                ->where('domain_id', $domainId)

                ->where('is_active', true)

                ->inRandomOrder()

                ->take(2)

                ->get();



            $selected = $selected->merge($domainQuestions);

        }



        // Top up if fewer than 8 found

        if ($selected->count() < $limit) {

            $usedIds = $selected->pluck('id');



            $extra = Question::with('domain')

                ->where('is_active', true)

                ->whereNotIn('id', $usedIds)

                ->inRandomOrder()

                ->take($limit - $selected->count())

                ->get();



            $selected = $selected->merge($extra);

        }



        return $selected->take($limit)->values();

    }

}

