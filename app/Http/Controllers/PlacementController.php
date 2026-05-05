<?php

namespace App\Http\Controllers;

use App\Models\Placement;
use Illuminate\Support\Facades\DB;

class PlacementController extends Controller
{
    /**
     * List view — capacity overview, filters, expiry warnings.
     * Also passes placements to the map tab so one page covers both views.
     */
    public function map()
    {
        $placements = Placement::with('carer')
            ->orderByRaw("FIELD(status, 'active', 'under_review', 'inactive')")
            ->orderBy('location')
            ->get();

        $active     = $placements->where('status', 'active');
        $totalCap   = $active->sum('capacity');
        $totalOcc   = $active->sum('current_occupancy');
        $available  = max(0, $totalCap - $totalOcc);

        $expiringSoon = $placements->filter(
            fn($p) => $p->end_date && $p->end_date->diffInDays(now(), false) >= -30 && $p->end_date->isFuture()
        )->pluck('id')->flip();

        $mappable = $placements
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->values();

        return view('socialworker.placements.map', compact(
            'placements',
            'mappable',
            'totalCap',
            'totalOcc',
            'available',
            'expiringSoon',
        ));
    }

    public function apiPlacements()
    {
        $placements = Placement::with('caseFile')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'type'         => $p->type,
                'location'     => $p->location,
                'status'       => $p->status,
                'latitude'     => $p->latitude,
                'longitude'    => $p->longitude,
                'capacity'     => $p->capacity,
                'occupancy'    => $p->current_occupancy,
                'case_file_id' => $p->case_file_id,
                'start_date'   => $p->start_date?->format('d M Y'),
                'end_date'     => $p->end_date?->format('d M Y'),
            ]);

        return response()->json($placements);
    }

    public function index()
    {
        return $this->map();
    }
}
