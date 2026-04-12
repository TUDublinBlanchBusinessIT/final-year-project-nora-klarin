<?php

namespace Tests\Unit;

use App\Services\WellbeingScoringService;
use PHPUnit\Framework\TestCase;


class WellbeingScoringServiceTest extends TestCase
{
    private WellbeingScoringService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new WellbeingScoringService();
    }


    /** @test */
    public function it_normalises_minimum_positive_value_to_zero(): void
    {
        $score = $this->service->normalise(0, 0, 4, true);
        $this->assertEquals(0.0, $score);
    }

    /** @test */
    public function it_normalises_maximum_positive_value_to_100(): void
    {
        $score = $this->service->normalise(4, 0, 4, true);
        $this->assertEquals(100.0, $score);
    }

    /** @test */
    public function it_normalises_midpoint_positive_value_to_50(): void
    {
        $score = $this->service->normalise(2, 0, 4, true);
        $this->assertEquals(50.0, $score);
    }

    /** @test */
    public function it_reverse_codes_negative_items_correctly(): void
    {
        // For a negatively framed item, raw=0 (least worry) should map to 100 (best wellbeing)
        $score = $this->service->normalise(0, 0, 4, false);
        $this->assertEquals(100.0, $score);
    }

    /** @test */
    public function it_reverse_codes_max_negative_value_to_zero(): void
    {
        // raw=4 on a negatively framed item = worst possible = 0 wellbeing
        $score = $this->service->normalise(4, 0, 4, false);
        $this->assertEquals(0.0, $score);
    }

    /** @test */
    public function it_normalises_likert_3_scale_correctly(): void
    {
        // 3-point scale: 0,1,2
        $this->assertEquals(0.0,   $this->service->normalise(0, 0, 2, true));
        $this->assertEquals(50.0,  $this->service->normalise(1, 0, 2, true));
        $this->assertEquals(100.0, $this->service->normalise(2, 0, 2, true));
    }

    /** @test */
    public function it_normalises_slider_0_to_10_scale(): void
    {
        $this->assertEquals(0.0,   $this->service->normalise(0,  0, 10, true));
        $this->assertEquals(50.0,  $this->service->normalise(5,  0, 10, true));
        $this->assertEquals(100.0, $this->service->normalise(10, 0, 10, true));
    }

    /** @test */
    public function it_returns_50_for_degenerate_scale_where_min_equals_max(): void
    {
        $score = $this->service->normalise(3, 3, 3, true);
        $this->assertEquals(50.0, $score);
    }

    /** @test */
    public function it_rounds_to_two_decimal_places(): void
    {
        // 1/3 = 0.3333... -> normalised = 33.33
        $score = $this->service->normalise(1, 0, 3, true);
        $this->assertEquals(33.33, $score);
    }

    // ── riskContribution() ────────────────────────────────────────────────────

    /** @test */
    public function it_returns_zero_risk_for_perfect_wellbeing_score(): void
    {
        $risk = $this->service->riskContribution(100.0, 4.0);
        $this->assertEquals(0.0, $risk);
    }

    /** @test */
    public function it_returns_maximum_risk_for_zero_wellbeing_score_with_critical_weight(): void
    {
        // (100 - 0) * 4.0 = 400
        $risk = $this->service->riskContribution(0.0, 4.0);
        $this->assertEquals(400.0, $risk);
    }

    /** @test */
    public function it_calculates_risk_contribution_for_medium_weight(): void
    {
        // wb_score = 40, weight = 1.0 -> (100 - 40) * 1.0 = 60
        $risk = $this->service->riskContribution(40.0, 1.0);
        $this->assertEquals(60.0, $risk);
    }

    /** @test */
    public function it_calculates_risk_contribution_for_low_weight(): void
    {
        // wb_score = 60, weight = 0.5 -> (100 - 60) * 0.5 = 20
        $risk = $this->service->riskContribution(60.0, 0.5);
        $this->assertEquals(20.0, $risk);
    }

    /** @test */
    public function it_calculates_risk_contribution_for_high_weight(): void
    {
        // wb_score = 25, weight = 2.0 -> (100 - 25) * 2.0 = 150
        $risk = $this->service->riskContribution(25.0, 2.0);
        $this->assertEquals(150.0, $risk);
    }

    // ── classifyRisk() ────────────────────────────────────────────────────────

    /** @test */
    public function it_classifies_zero_risk_as_low(): void
    {
        $this->assertEquals('low', $this->service->classifyRisk(0.0));
    }

    /** @test */
    public function it_classifies_score_below_80_as_low(): void
    {
        $this->assertEquals('low', $this->service->classifyRisk(79.9));
    }

    /** @test */
    public function it_classifies_score_of_80_as_moderate(): void
    {
        $this->assertEquals('moderate', $this->service->classifyRisk(80.0));
    }

    /** @test */
    public function it_classifies_score_of_150_as_high(): void
    {
        $this->assertEquals('high', $this->service->classifyRisk(150.0));
    }

    /** @test */
    public function it_classifies_score_of_250_as_critical(): void
    {
        $this->assertEquals('critical', $this->service->classifyRisk(250.0));
    }

    /** @test */
    public function it_classifies_single_critical_question_at_zero_as_critical(): void
    {
        // A critical safety question (weight 4.0) answered at minimum wellbeing
        // should immediately reach critical: (100 - 0) * 4.0 = 400
        $risk = $this->service->riskContribution(0.0, 4.0);
        $this->assertEquals('critical', $this->service->classifyRisk($risk));
    }

    /** @test */
    public function it_classifies_five_medium_questions_at_low_wellbeing_correctly(): void
    {
        // 5 medium questions (weight 1.0) each at wb_score 30
        // Each contributes (100 - 30) * 1.0 = 70, total = 350 -> critical
        $totalRisk = 5 * $this->service->riskContribution(30.0, 1.0);
        $this->assertEquals('critical', $this->service->classifyRisk($totalRisk));
    }

    // ── getRiskWeight() ───────────────────────────────────────────────────────

    /** @test */
    public function it_returns_correct_weight_for_each_risk_level(): void
    {
        $this->assertEquals(0.5, $this->service->getRiskWeight('low'));
        $this->assertEquals(1.0, $this->service->getRiskWeight('medium'));
        $this->assertEquals(2.0, $this->service->getRiskWeight('high'));
        $this->assertEquals(4.0, $this->service->getRiskWeight('critical'));
    }

    /** @test */
    public function it_returns_default_weight_for_unknown_risk_level(): void
    {
        $this->assertEquals(1.0, $this->service->getRiskWeight('unknown'));
    }

    // ── Integration: formula chain ────────────────────────────────────────────

    /** @test */
    public function full_scoring_chain_produces_expected_output_for_positive_item(): void
    {
        // A positively framed likert_5 question (0-4 scale, medium risk)
        // answered at raw=1 (second lowest)
        $wbScore = $this->service->normalise(1, 0, 4, true);
        $this->assertEquals(25.0, $wbScore);

        $risk = $this->service->riskContribution($wbScore, 1.0);
        $this->assertEquals(75.0, $risk);
    }

    /** @test */
    public function full_scoring_chain_produces_expected_output_for_negative_item(): void
    {
        // A negatively framed likert_5 question answered at raw=3 (high frequency of problem)
        // Should reverse-code: normalised = (3-0)/(4-0) = 0.75, reversed = 0.25, wb = 25
        $wbScore = $this->service->normalise(3, 0, 4, false);
        $this->assertEquals(25.0, $wbScore);

        // High risk weight (e.g. a safety question)
        $risk = $this->service->riskContribution($wbScore, 2.0);
        $this->assertEquals(150.0, $risk);

        // 150 should classify as high
        $this->assertEquals('high', $this->service->classifyRisk($risk));
    }
}
