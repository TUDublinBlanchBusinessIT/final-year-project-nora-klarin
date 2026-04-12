<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionWording;
use App\Models\User;
use App\Models\WellbeingCheck;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CheckQuestionSelector
 *
 * Selects and resolves a set of questions for the next wellbeing check
 * for a given young person.
 *
 * Selection algorithm (applied in priority order):
 *
 *   1. EXCLUSION — remove questions asked in the last N checks (rotation)
 *   2. SAFETY FLOOR — always allocate at least 1 safety question slot;
 *      allocate 2 if any safety tag fired in the last check
 *   3. GOAL PRIORITY — add 1 extra slot per domain with an active goal
 *   4. LOW SCORE PRIORITY — add 1 extra slot per domain that scored below
 *      the low-score threshold in the last check
 *   5. BASE COVERAGE — ensure every domain has at least 1 slot
 *   6. FILL TO TARGET — distribute remaining slots to under-represented domains
 *   7. WITHIN-DOMAIN SELECTION — from each domain's slot allocation, pick
 *      questions weighted by tag recurrence, excluding recently used ones
 *   8. WORDING RESOLUTION — replace question text with age-appropriate
 *      wording from question_wordings, falling back to questions.text
 *
 * This implements the adaptive check composition described in the system
 * design document, ensuring checks are comprehensive (all domains touched),
 * responsive (low scores and active goals get more questions), and varied
 * (rotation prevents repetition), consistent with recommendations for
 * longitudinal monitoring tools (NCB, 2017).
 *
 * Target check length is configurable via TARGET_QUESTION_COUNT.
 * Rotation window (how many past checks to exclude from) is configurable
 * via ROTATION_WINDOW.
 */
class CheckQuestionSelector
{
    // Total number of questions to include in a single check
    private const TARGET_QUESTION_COUNT = 8;

    // How many previous checks to look back when excluding recently used questions
    private const ROTATION_WINDOW = 2;

    // Domain wb_score below this adds a priority slot for that domain
    private const LOW_SCORE_PRIORITY_THRESHOLD = 45;

    // Minimum questions per domain regardless of allocation logic
    private const MIN_PER_DOMAIN = 1;

    // Safety domain name — must match exactly what is seeded in domains table
    private const SAFETY_DOMAIN = 'Safety';

    /**
     * Build a resolved question list for the next check.
     *
     * Returns a Collection of plain objects, each with:
     *   id, domain_id, domain_name, text (age-resolved), response_type,
     *   min_value, max_value, is_positive, risk_level, risk_weight
     *
     * @param  User $youngPerson
     * @return Collection
     */
    public function selectFor(User $youngPerson): Collection
    {
        $age            = $this->resolveAge($youngPerson);
        $lastCheck      = $this->getLastCheck($youngPerson);
        $excludedIds    = $this->getRecentlyUsedQuestionIds($youngPerson);
        $domainIds      = $this->getAllDomainIds();
        $safetyDomainId = $this->getSafetyDomainId();

        // Build the slot allocation map: domain_id -> number of question slots
        $slotMap = $this->buildSlotMap(
            $youngPerson,
            $lastCheck,
            $domainIds,
            $safetyDomainId
        );

        Log::debug('CheckQuestionSelector slot map', [
            'young_person_id' => $youngPerson->id,
            'slot_map'        => $slotMap,
            'excluded_ids'    => $excludedIds->toArray(),
        ]);

        // Select questions to fill each domain's slot allocation
        $selected = $this->fillSlots($slotMap, $excludedIds, $age, $lastCheck);

        // Resolve age-appropriate wording for each selected question
        return $this->resolveWordings($selected, $age);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 1 — Exclusion: recently used questions
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns IDs of questions asked in the last ROTATION_WINDOW checks
     * for this young person. These are excluded from selection.
     */
    private function getRecentlyUsedQuestionIds(User $youngPerson): Collection
    {
        $recentCheckIds = WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->limit(self::ROTATION_WINDOW)
            ->pluck('id');

        if ($recentCheckIds->isEmpty()) {
            return collect();
        }

        return DB::table('check_question_log')
            ->whereIn('wellbeing_check_id', $recentCheckIds)
            ->pluck('question_id')
            ->unique();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Steps 2–6 — Slot allocation
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Builds the slot map: how many questions should be selected from each domain.
     *
     * @return array<int, int>  domain_id => slot_count
     */
    private function buildSlotMap(
        User $youngPerson,
        ?WellbeingCheck $lastCheck,
        Collection $domainIds,
        int $safetyDomainId
    ): array {
        // Initialise every domain with the minimum slot count
        $slots = $domainIds->mapWithKeys(fn($id) => [$id => self::MIN_PER_DOMAIN])->toArray();

        // ── Rule 2: Safety floor ──────────────────────────────────────────────
        // Always at least 1 safety question. Add a second if a safety tag fired
        // in the last check.
        $slots[$safetyDomainId] = max($slots[$safetyDomainId], 1);

        if ($lastCheck && $this->safetyTagFiredInLastCheck($lastCheck)) {
            $slots[$safetyDomainId] = max($slots[$safetyDomainId], 2);
        }

        // ── Rule 3: Goal priority ─────────────────────────────────────────────
        // Add 1 extra slot per domain that has an active goal assigned to
        // this young person's current case file.
        $activeGoalDomainIds = $this->getActiveGoalDomainIds($youngPerson);

        foreach ($activeGoalDomainIds as $domainId) {
            if (isset($slots[$domainId])) {
                $slots[$domainId]++;
            }
        }

        // ── Rule 4: Low score priority ────────────────────────────────────────
        // Add 1 extra slot for any domain that scored below the threshold
        // in the last check.
        if ($lastCheck) {
            $lowScoringDomainIds = $this->getLowScoringDomainIds($lastCheck);

            foreach ($lowScoringDomainIds as $domainId) {
                if (isset($slots[$domainId])) {
                    $slots[$domainId]++;
                }
            }
        }

        // ── Rules 5 & 6: Base coverage already applied (MIN_PER_DOMAIN = 1)
        // Fill remaining budget up to TARGET_QUESTION_COUNT
        $slots = $this->fillRemainingBudget($slots, $domainIds);

        return $slots;
    }

    /**
     * Distributes any remaining question budget after priority rules are applied.
     * Adds slots to domains that currently have the minimum allocation first,
     * cycling through domains until the target count is reached.
     *
     * @param  array      $slots
     * @param  Collection $domainIds
     * @return array
     */
    private function fillRemainingBudget(array $slots, Collection $domainIds): array
    {
        $currentTotal = array_sum($slots);
        $remaining    = self::TARGET_QUESTION_COUNT - $currentTotal;

        if ($remaining <= 0) {
            return $slots;
        }

        // Sort domains by current slot count ascending so we top up the
        // least-represented domains first
        asort($slots);
        $domainIdList = array_keys($slots);
        $i = 0;

        while ($remaining > 0) {
            $domainId = $domainIdList[$i % count($domainIdList)];
            $slots[$domainId]++;
            $remaining--;
            $i++;
        }

        return $slots;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 7 — Within-domain question selection
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * For each domain, selects the allocated number of questions from the
     * available candidate pool (active, age-appropriate, not recently used).
     *
     * Preference logic within a domain:
     *   - Questions whose tags appeared frequently in recent checks (high
     *     recurrence) are weighted higher — they represent persistent patterns
     *   - Among equally weighted candidates, selection is random to ensure
     *     variety across checks
     *
     * Falls back to any available question if the preferred pool is exhausted.
     */
    private function fillSlots(
        array $slots,
        Collection $excludedIds,
        int $age,
        ?WellbeingCheck $lastCheck
    ): Collection {
        $selected       = collect();
        $tagRecurrence  = $this->getTagRecurrence($lastCheck);

        foreach ($slots as $domainId => $slotCount) {
            $candidates = $this->getCandidates($domainId, $excludedIds, $age);

            if ($candidates->isEmpty()) {
                // Fallback: allow recently used questions if pool is exhausted
                $candidates = $this->getCandidates($domainId, collect(), $age);

                Log::debug('CheckQuestionSelector: candidate pool exhausted, fallback to full pool', [
                    'domain_id' => $domainId,
                ]);
            }

            if ($candidates->isEmpty()) {
                Log::warning('CheckQuestionSelector: no questions available for domain', [
                    'domain_id' => $domainId,
                ]);
                continue;
            }

            // Score each candidate by tag recurrence weight
            $scored = $candidates->map(function ($question) use ($tagRecurrence) {
                $tagIds = DB::table('question_tag')
                    ->where('question_id', $question->id)
                    ->pluck('tag_id');

                // Sum the recurrence count for all tags on this question
                $recurrenceScore = $tagIds->sum(fn($tid) => $tagRecurrence->get($tid, 0));

                return (object) array_merge(
                    (array) $question,
                    ['recurrence_score' => $recurrenceScore]
                );
            });

            // Sort by recurrence score descending, then shuffle within ties
            $sorted = $scored
                ->sortByDesc('recurrence_score')
                ->values();

            // Take the top N, but randomise among questions with equal recurrence
            // to avoid always picking the same question when scores are identical
            $pick = $sorted
                ->groupBy('recurrence_score')
                ->map(fn($group) => $group->shuffle())
                ->flatten()
                ->take($slotCount);

            $selected = $selected->merge($pick);

            // Track selected IDs to avoid selecting the same question twice
            // across different domain allocations (safety questions could
            // theoretically appear in both safety and another domain's pool
            // if a question is misconfigured — this guards against it)
            $excludedIds = $excludedIds->merge($pick->pluck('id'));
        }

        return $selected;
    }

    /**
     * Returns active question candidates for a domain:
     *   - is_active = true
     *   - age_band_min <= $age <= age_band_max (or no age band set)
     *   - id not in $excludedIds
     */
    private function getCandidates(int $domainId, Collection $excludedIds, int $age): Collection
    {
        return Question::where('domain_id', $domainId)
            ->where('is_active', true)
            ->where(function ($q) use ($age) {
                $q->whereNull('age_band_min')
                  ->orWhere(function ($q2) use ($age) {
                      $q2->where('age_band_min', '<=', $age)
                         ->where('age_band_max', '>=', $age);
                  });
            })
            ->when($excludedIds->isNotEmpty(), fn($q) => $q->whereNotIn('id', $excludedIds))
            ->with('domain')
            ->get();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Step 8 — Wording resolution
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolves age-appropriate wording for each selected question.
     *
     * Lookup order:
     *   1. question_wordings where age_min <= age <= age_max
     *   2. questions.text (default fallback)
     *
     * Returns a clean Collection of plain objects ready for API serialisation.
     */
    private function resolveWordings(Collection $questions, int $age): Collection
    {
        $questionIds = $questions->pluck('id');

        // Fetch all relevant wordings in one query
        $wordings = DB::table('question_wordings')
            ->whereIn('question_id', $questionIds)
            ->where('age_min', '<=', $age)
            ->where('age_max', '>=', $age)
            ->get()
            ->keyBy('question_id');

        return $questions->map(function ($question) use ($wordings) {
            $wording = $wordings->get($question->id);

            return (object) [
                'id'            => $question->id,
                'domain_id'     => $question->domain_id,
                'domain_name'   => $question->domain->name ?? null,
                'text'          => $wording?->text ?? $question->text,
                'response_type' => $question->response_type,
                'min_value'     => $question->min_value,
                'max_value'     => $question->max_value,
                'is_positive'   => (bool) $question->is_positive,
                'risk_level'    => $question->risk_level,
            ];
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Supporting queries
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns domain IDs that have an active (pending or in_progress) goal
     * assigned to this young person's case file.
     */
    private function getActiveGoalDomainIds(User $youngPerson): Collection
    {
        return DB::table('case_goals')
            ->join('case_files', 'case_goals.case_file_id', '=', 'case_files.id')
            ->join('goals', 'case_goals.goal_id', '=', 'goals.id')
            ->where('case_files.young_person_id', $youngPerson->id)
            ->where('case_files.status', 'open')
            ->whereIn('case_goals.status', ['pending', 'in_progress'])
            ->whereNotNull('goals.source_domain_id')
            ->pluck('goals.source_domain_id')
            ->unique();
    }

    /**
     * Returns domain IDs where the average wellbeing score in the given check
     * was below LOW_SCORE_PRIORITY_THRESHOLD.
     */
    private function getLowScoringDomainIds(WellbeingCheck $check): Collection
    {
        return $check->domainScores()
            ->where('average_score', '<', self::LOW_SCORE_PRIORITY_THRESHOLD)
            ->pluck('domain_id');
    }

    /**
     * Returns true if any safeguarding tag (alert_override = true) fired
     * in the given check — indicating the next check should have an
     * elevated safety question allocation.
     */
    private function safetyTagFiredInLastCheck(WellbeingCheck $check): bool
    {
        return DB::table('alerts')
            ->where('wellbeing_check_id', $check->id)
            ->where('alert_type', 'tag_override')
            ->exists();
    }

    /**
     * Builds a map of tag_id => recurrence count across recent checks.
     *
     * Used to weight question selection toward questions whose tags have
     * appeared persistently — a signal of ongoing concern in that area.
     *
     * Looks back ROTATION_WINDOW checks, same as the exclusion window.
     */
    private function getTagRecurrence(?WellbeingCheck $lastCheck): Collection
    {
        if (!$lastCheck) {
            return collect();
        }

        $recentCheckIds = WellbeingCheck::where('young_person_id', $lastCheck->young_person_id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->limit(self::ROTATION_WINDOW)
            ->pluck('id');

        if ($recentCheckIds->isEmpty()) {
            return collect();
        }

        // Count how many times each tag appeared across responses in recent checks
        return DB::table('wellbeing_responses')
            ->join('question_tag', 'wellbeing_responses.question_id', '=', 'question_tag.question_id')
            ->whereIn('wellbeing_responses.wellbeing_check_id', $recentCheckIds)
            ->select('question_tag.tag_id', DB::raw('COUNT(*) as count'))
            ->groupBy('question_tag.tag_id')
            ->pluck('count', 'tag_id');
    }

    /**
     * Returns the most recent completed check for the young person, or null
     * if this is their first check (intake).
     */
    private function getLastCheck(User $youngPerson): ?WellbeingCheck
    {
        return WellbeingCheck::where('young_person_id', $youngPerson->id)
            ->whereNotNull('completed_at')
            ->with('domainScores')
            ->orderByDesc('completed_at')
            ->first();
    }

    /**
     * Returns all domain IDs as a flat collection.
     */
    private function getAllDomainIds(): Collection
    {
        return DB::table('domains')->pluck('id');
    }

    /**
     * Returns the ID of the Safety domain.
     */
    private function getSafetyDomainId(): int
    {
        return DB::table('domains')
            ->where('name', self::SAFETY_DOMAIN)
            ->value('id');
    }

    /**
     * Resolves the young person's current age in whole years from their dob.
     * Falls back to 13 (middle of the age range) if dob is not set.
     */
    private function resolveAge(User $youngPerson): int
    {
        if (!$youngPerson->dob) {
            Log::warning('CheckQuestionSelector: young person has no dob, defaulting age to 13', [
                'young_person_id' => $youngPerson->id,
            ]);
            return 13;
        }

        return (int) now()->diffInYears($youngPerson->dob);
    }
}
