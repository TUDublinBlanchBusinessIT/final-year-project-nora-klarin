<?php



namespace App\Services;



class WellbeingScoringService

{

    public function normalise($raw, $min, $max, $isPositive)

    {

        if ($max == $min) {

            return 0;

        }



        $normalized = ($raw - $min) / ($max - $min);



        if (!$isPositive) {

            $normalized = 1 - $normalized;

        }



        return round($normalized * 100, 2);

    }



    public function riskContribution($wbScore, $weight)

    {

        $weight = $weight ?? 1;



        return round((100 - $wbScore) * $weight, 2);

    }



    public function calculateDomainScores($check)

    {

        $responses = $check->responses()->with('question.domain')->get();



        $grouped = $responses->filter(function ($response) {

            return $response->question && $response->question->domain_id;

        })->groupBy(function ($response) {

            return $response->question->domain_id;

        });



        foreach ($grouped as $domainId => $items) {

            $avgScore = round($items->avg('normalized_score') ?? 0, 2);

            $riskScore = round($items->sum('risk_score') ?? 0, 2);



            $check->domainScores()->updateOrCreate(

                [

                    'domain_id' => $domainId,

                ],

                [

                    'average_score' => $avgScore,

                    'risk_score' => $riskScore,

                ]

            );

        }

    }



    public function overallFromDomains($check)

    {

        return round($check->domainScores()->avg('average_score') ?? 0, 2);

    }



    public function overallRiskFromDomains($check)

    {

        return round($check->domainScores()->sum('risk_score') ?? 0, 2);

    }



    public function classifyRisk($riskScore)

    {

        if ($riskScore >= 250) {

            return 'critical';

        }



        if ($riskScore >= 150) {

            return 'high';

        }



        if ($riskScore >= 80) {

            return 'moderate';

        }



        return 'low';

    }



    public function analyseTagPatterns($check)

    {

        $responses = $check->responses()->with('question.tags')->get();



        $tagScores = [];

        $triggeredSafeguardingTags = [];

        $safeguardingTriggered = false;



        foreach ($responses as $response) {

            if (!$response->question) {

                continue;

            }



            foreach ($response->question->tags as $tag) {

                if (!isset($tagScores[$tag->name])) {

                    $tagScores[$tag->name] = [

                        'count' => 0,

                        'average_score' => 0,

                        'category' => $tag->category ?? null,

                    ];

                }



                $tagScores[$tag->name]['count']++;

                $tagScores[$tag->name]['average_score'] += ($response->normalized_score ?? 0);



                $isSafeguardingTag =

                    ($tag->category ?? null) === 'safeguarding' ||

                    ($tag->alert_override ?? false);



                $threshold = $tag->alert_threshold ?? 40;



                if ($isSafeguardingTag && ($response->normalized_score ?? 0) < $threshold) {

                    $safeguardingTriggered = true;

                    $triggeredSafeguardingTags[] = $tag->name;

                }

            }

        }



        foreach ($tagScores as $tagName => $data) {

            $tagScores[$tagName]['average_score'] = $data['count'] > 0

                ? round($data['average_score'] / $data['count'], 2)

                : 0;

        }



        $check->update([

            'tag_summary' => [

                'all_tags' => $tagScores,

                'triggered_safeguarding_tags' => array_values(array_unique($triggeredSafeguardingTags)),

            ],

            'safeguarding_flag' => $safeguardingTriggered,

        ]);



        return $safeguardingTriggered;

    }

}

