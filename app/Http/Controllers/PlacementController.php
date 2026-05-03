<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Placement;

class PlacementController extends Controller
{
      public function index()
    {
        return view('socialworker.placements.map');
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
    public function map()
{
$placements = Placement::select(
    'id',
    'location',
    'capacity',
    'current_occupancy',
    'status',
    'latitude',
    'longitude'
)->get();

    return view('socialworker.placements.map', compact('placements'));
}
}
