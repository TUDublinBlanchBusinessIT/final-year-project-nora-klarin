<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Placement;

class PlacementController extends Controller
{
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
