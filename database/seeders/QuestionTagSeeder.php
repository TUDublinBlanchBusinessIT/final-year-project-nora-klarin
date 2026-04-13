<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionTagSeeder extends Seeder
{
    public function run(): void
    {
        // Truncated in QuestionSeeder already, but guard here too
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('question_tag')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $mappings = [
            // ── Emotional (Q1–Q10) ─────────────────────────────────────────

            // Q1: cheerful / good spirits → optimism, life_satisfaction
            ['question_id' => 1,  'tag_id' => 34], // optimism
            ['question_id' => 1,  'tag_id' => 40], // life_satisfaction

            // Q2: calm and relaxed → anxiety, emotional_regulation
            ['question_id' => 2,  'tag_id' => 9],  // anxiety
            ['question_id' => 2,  'tag_id' => 27], // emotional_regulation

            // Q3: woke up fresh/rested → sleep_disruption
            ['question_id' => 3,  'tag_id' => 17], // sleep_disruption

            // Q4: feeling low/down → depression_signs, emotional_dysregulation
            ['question_id' => 4,  'tag_id' => 10], // depression_signs
            ['question_id' => 4,  'tag_id' => 13], // emotional_dysregulation

            // Q5: nervous/anxious → anxiety, stress
            ['question_id' => 5,  'tag_id' => 9],  // anxiety
            ['question_id' => 5,  'tag_id' => 16], // stress

            // Q6: lonely → social_isolation, depression_signs
            ['question_id' => 6,  'tag_id' => 20], // social_isolation
            ['question_id' => 6,  'tag_id' => 10], // depression_signs

            // Q7: difficulties piling up → stress, emotional_dysregulation
            ['question_id' => 7,  'tag_id' => 16], // stress
            ['question_id' => 7,  'tag_id' => 13], // emotional_dysregulation

            // Q8: confident handling problems → self_efficacy, resilience
            ['question_id' => 8,  'tag_id' => 39], // self_efficacy
            ['question_id' => 8,  'tag_id' => 35], // resilience

            // Q9: daily life filled with interest → optimism, future_orientation
            ['question_id' => 9,  'tag_id' => 34], // optimism
            ['question_id' => 9,  'tag_id' => 37], // future_orientation

            // Q10: difficulty sleeping → sleep_disruption, anxiety
            ['question_id' => 10, 'tag_id' => 17], // sleep_disruption
            ['question_id' => 10, 'tag_id' => 9],  // anxiety

            // ── Behavioural (Q11–Q18) ──────────────────────────────────────

            // Q11: irritable/bad temper → emotional_dysregulation, conduct_issues
            ['question_id' => 11, 'tag_id' => 13], // emotional_dysregulation
            ['question_id' => 11, 'tag_id' => 25], // conduct_issues

            // Q12: hard to concentrate → concentration
            ['question_id' => 12, 'tag_id' => 26], // concentration

            // Q13: manage to do things → self_efficacy
            ['question_id' => 13, 'tag_id' => 39], // self_efficacy

            // Q14: find solution to problem → self_efficacy, resilience
            ['question_id' => 14, 'tag_id' => 39], // self_efficacy
            ['question_id' => 14, 'tag_id' => 35], // resilience

            // Q15: alcohol use → substance_use
            ['question_id' => 15, 'tag_id' => 14], // substance_use

            // Q16: smoking → substance_use
            ['question_id' => 16, 'tag_id' => 14], // substance_use

            // Q17: social media preoccupation → concentration
            ['question_id' => 17, 'tag_id' => 26], // concentration

            // Q18: feel in control of behaviour → emotional_regulation
            ['question_id' => 18, 'tag_id' => 27], // emotional_regulation

            // ── Social (Q19–Q26) ───────────────────────────────────────────

            // Q19: family tries to help → family_conflict (low = concern), attachment_issues
            ['question_id' => 19, 'tag_id' => 28], // family_conflict
            ['question_id' => 19, 'tag_id' => 12], // attachment_issues

            // Q20: can talk to family/carers → attachment_issues, placement_anxiety
            ['question_id' => 20, 'tag_id' => 12], // attachment_issues
            ['question_id' => 20, 'tag_id' => 29], // placement_anxiety

            // Q21: friends try to help → positive_relationships, social_isolation
            ['question_id' => 21, 'tag_id' => 31], // positive_relationships
            ['question_id' => 21, 'tag_id' => 20], // social_isolation

            // Q22: can count on friends → positive_relationships
            ['question_id' => 22, 'tag_id' => 31], // positive_relationships
            ['question_id' => 22, 'tag_id' => 36], // belonging

            // Q23: accepted by peers → belonging, low_self_esteem
            ['question_id' => 23, 'tag_id' => 36], // belonging
            ['question_id' => 23, 'tag_id' => 18], // low_self_esteem

            // Q24: talk to carer → attachment_issues, placement_anxiety
            ['question_id' => 24, 'tag_id' => 12], // attachment_issues
            ['question_id' => 24, 'tag_id' => 29], // placement_anxiety

            // Q25: time with friends → social_isolation, positive_relationships
            ['question_id' => 25, 'tag_id' => 20], // social_isolation
            ['question_id' => 25, 'tag_id' => 31], // positive_relationships

            // Q26: classmates kind/helpful → belonging, peer_conflict
            ['question_id' => 26, 'tag_id' => 36], // belonging
            ['question_id' => 26, 'tag_id' => 19], // peer_conflict

            // ── Physical (Q27–Q34) ─────────────────────────────────────────

            // Q27: self-rated health → physical_complaints
            ['question_id' => 27, 'tag_id' => 38], // physical_complaints

            // Q28: headaches → physical_complaints, stress
            ['question_id' => 28, 'tag_id' => 38], // physical_complaints
            ['question_id' => 28, 'tag_id' => 16], // stress

            // Q29: stomachaches → physical_complaints, anxiety
            ['question_id' => 29, 'tag_id' => 38], // physical_complaints
            ['question_id' => 29, 'tag_id' => 9],  // anxiety

            // Q30: MVPA 60 mins → physical_inactivity
            ['question_id' => 30, 'tag_id' => 23], // physical_inactivity

            // Q31: vigorous exercise → physical_inactivity
            ['question_id' => 31, 'tag_id' => 23], // physical_inactivity

            // Q32: breakfast → poor_nutrition
            ['question_id' => 32, 'tag_id' => 24], // poor_nutrition

            // Q33: fruit consumption → poor_nutrition
            ['question_id' => 33, 'tag_id' => 24], // poor_nutrition

            // Q34: body image → body_image, disordered_eating
            ['question_id' => 34, 'tag_id' => 32], // body_image
            ['question_id' => 34, 'tag_id' => 15], // disordered_eating

            // ── Education (Q35–Q40) ────────────────────────────────────────

            // Q35: school satisfaction → school_disengagement
            ['question_id' => 35, 'tag_id' => 21], // school_disengagement

            // Q36: schoolwork pressure → stress, school_disengagement
            ['question_id' => 36, 'tag_id' => 16], // stress
            ['question_id' => 36, 'tag_id' => 21], // school_disengagement

            // Q37: teachers care → belonging, low_self_esteem
            ['question_id' => 37, 'tag_id' => 36], // belonging
            ['question_id' => 37, 'tag_id' => 18], // low_self_esteem

            // Q38: teachers accept me → identity, belonging
            ['question_id' => 38, 'tag_id' => 30], // identity
            ['question_id' => 38, 'tag_id' => 36], // belonging

            // Q39: school attendance → attendance_concern
            ['question_id' => 39, 'tag_id' => 22], // attendance_concern

            // Q40: academic confidence → low_self_esteem, self_efficacy
            ['question_id' => 40, 'tag_id' => 18], // low_self_esteem
            ['question_id' => 40, 'tag_id' => 39], // self_efficacy

            // ── Safety (Q41–Q46) ───────────────────────────────────────────

            // Q41: feel safe at home → abuse_physical, abuse_emotional, neglect
            ['question_id' => 41, 'tag_id' => 3],  // abuse_physical
            ['question_id' => 41, 'tag_id' => 4],  // abuse_emotional
            ['question_id' => 41, 'tag_id' => 6],  // neglect

            // Q42: feel safe at school → bullying_victim
            ['question_id' => 42, 'tag_id' => 33], // bullying_victim

            // Q43: bullied at school → bullying_victim, trauma_response
            ['question_id' => 43, 'tag_id' => 33], // bullying_victim
            ['question_id' => 43, 'tag_id' => 11], // trauma_response

            // Q44: cyberbullied → bullying_victim, anxiety
            ['question_id' => 44, 'tag_id' => 33], // bullying_victim
            ['question_id' => 44, 'tag_id' => 9],  // anxiety

            // Q45: physical fighting → conduct_issues, trauma_response
            ['question_id' => 45, 'tag_id' => 25], // conduct_issues
            ['question_id' => 45, 'tag_id' => 11], // trauma_response

            // Q46: trusted adult → neglect, attachment_issues
            ['question_id' => 46, 'tag_id' => 6],  // neglect
            ['question_id' => 46, 'tag_id' => 12], // attachment_issues

            // ── Life Satisfaction (Q47–Q50) ────────────────────────────────

            // Q47: Cantril ladder → life_satisfaction, future_orientation
            ['question_id' => 47, 'tag_id' => 40], // life_satisfaction
            ['question_id' => 47, 'tag_id' => 37], // future_orientation

            // Q48: overall happiness → life_satisfaction, optimism
            ['question_id' => 48, 'tag_id' => 40], // life_satisfaction
            ['question_id' => 48, 'tag_id' => 34], // optimism

            // Q49: satisfied with self → low_self_esteem, identity
            ['question_id' => 49, 'tag_id' => 18], // low_self_esteem
            ['question_id' => 49, 'tag_id' => 30], // identity

            // Q50: life going well → optimism, resilience
            ['question_id' => 50, 'tag_id' => 34], // optimism
            ['question_id' => 50, 'tag_id' => 35], // resilience
        ];

        foreach ($mappings as $mapping) {
            DB::table('question_tag')->insert($mapping);
        }
    }
}
