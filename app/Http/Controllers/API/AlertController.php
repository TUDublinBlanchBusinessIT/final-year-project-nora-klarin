<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AlertController
 *
 * Manages alert retrieval and acknowledgement for social workers.
 *
 *   GET   /api/alerts/unacknowledged          — all unacknowledged alerts for the worker's cases
 *   GET   /api/alerts/{youngPerson}           — all alerts for a specific young person
 *   PATCH /api/alerts/{alert}/acknowledge     — mark a single alert as acknowledged
 *   PATCH /api/alerts/acknowledge-all         — bulk acknowledge all alerts for a young person
 */
class AlertController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/alerts/unacknowledged
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns all unacknowledged alerts across all young people assigned to
     * the authenticated social worker, ordered by severity then date.
     *
     * Severity order: critical > high > medium > low
     *
     * Used to render the social worker's alert dashboard/notification panel.
     *
     * Optional query params:
     *   ?severity=critical   — filter to one severity level
     *   ?type=tag_override   — filter to one alert type
     */
    public function unacknowledged(Request $request): JsonResponse
    {
        $this->authorize('viewAlerts');

        /** @var User $worker */
        $worker = Auth::user();

        // Get IDs of young people assigned to this worker's cases
        $youngPersonIds = $this->getAssignedYoungPersonIds($worker);

        $query = Alert::whereIn('young_person_id', $youngPersonIds)
            ->whereNull('acknowledged_at')
            ->with(['youngPerson:id,name', 'domain:id,name', 'tag:id,name,category'])
            ->orderByRaw("FIELD(severity, 'critical','high','medium','low')")
            ->orderByDesc('created_at');

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('type')) {
            $query->where('alert_type', $request->input('type'));
        }

        $alerts = $query->get();

        return response()->json([
            'count'  => $alerts->count(),
            'alerts' => $alerts->map(fn($alert) => $this->formatAlert($alert)),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /api/alerts/{youngPerson}
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns the full alert history for a specific young person.
     * Includes acknowledged alerts, ordered newest first.
     *
     * Used on the individual child's profile/dashboard page.
     *
     * Optional query params:
     *   ?unacknowledged_only=true
     *   ?limit=20
     */
    public function forYoungPerson(Request $request, User $youngPerson): JsonResponse
    {
        $this->authorize('viewAlerts');

        $limit = min((int) $request->input('limit', 20), 100);

        $query = Alert::where('young_person_id', $youngPerson->id)
            ->with(['domain:id,name', 'tag:id,name,category'])
            ->orderByDesc('created_at')
            ->limit($limit);

        if ($request->boolean('unacknowledged_only')) {
            $query->whereNull('acknowledged_at');
        }

        $alerts = $query->get();

        return response()->json([
            'young_person_id' => $youngPerson->id,
            'count'           => $alerts->count(),
            'alerts'          => $alerts->map(fn($alert) => $this->formatAlert($alert)),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PATCH /api/alerts/{alert}/acknowledge
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Marks a single alert as acknowledged by the authenticated social worker.
     *
     * Records who acknowledged it and when, for audit purposes.
     * Returns 409 if already acknowledged.
     */
    public function acknowledge(Alert $alert): JsonResponse
    {
        $this->authorize('acknowledgeAlert', $alert);

        if ($alert->acknowledged_at !== null) {
            return response()->json([
                'message'         => 'Alert already acknowledged.',
                'acknowledged_at' => $alert->acknowledged_at,
                'acknowledged_by' => $alert->acknowledged_by,
            ], 409);
        }

        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => Auth::id(),
        ]);

        return response()->json([
            'message'         => 'Alert acknowledged.',
            'alert_id'        => $alert->id,
            'acknowledged_at' => $alert->acknowledged_at,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PATCH /api/alerts/acknowledge-all
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Bulk acknowledges all unacknowledged alerts for a given young person.
     *
     * Used when a social worker reviews a child's full alert list and
     * wants to clear them all in one action.
     *
     * Expects: { "young_person_id": 5 }
     */
    public function acknowledgeAll(Request $request): JsonResponse
    {
        $this->authorize('viewAlerts');

        $request->validate([
            'young_person_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $count = Alert::where('young_person_id', $request->input('young_person_id'))
            ->whereNull('acknowledged_at')
            ->update([
                'acknowledged_at' => now(),
                'acknowledged_by' => Auth::id(),
            ]);

        return response()->json([
            'message'              => "{$count} alert(s) acknowledged.",
            'alerts_acknowledged'  => $count,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Formats an Alert model into a consistent API response shape.
     */
    private function formatAlert(Alert $alert): array
    {
        return [
            'id'              => $alert->id,
            'alert_type'      => $alert->alert_type,
            'severity'        => $alert->severity,
            'message'         => $alert->message,
            'domain'          => $alert->domain?->name,
            'tag'             => $alert->tag?->name,
            'tag_category'    => $alert->tag?->category,
            'young_person_id' => $alert->young_person_id,
            'young_person'    => $alert->youngPerson?->name,
            'check_id'        => $alert->wellbeing_check_id,
            'acknowledged_at' => $alert->acknowledged_at,
            'acknowledged_by' => $alert->acknowledged_by,
            'created_at'      => $alert->created_at,
        ];
    }

    /**
     * Returns the IDs of young people whose cases are assigned to this worker.
     */
    private function getAssignedYoungPersonIds(User $worker): array
    {
        return \DB::table('case_user')
            ->join('case_files', 'case_user.case_file_id', '=', 'case_files.id')
            ->where('case_user.user_id', $worker->id)
            ->where('case_user.role', 'social_worker')
            ->pluck('case_files.young_person_id')
            ->unique()
            ->toArray();
    }
}
