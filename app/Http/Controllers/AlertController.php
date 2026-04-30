<?php

namespace App\Http\Controllers;

use App\Models\Alert;

class AlertController extends Controller
{
    public function acknowledge(Alert $alert)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $alert->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Alert acknowledged.');
    }
}
