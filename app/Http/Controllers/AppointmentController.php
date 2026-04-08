<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'date'  => ['required', 'date'],
            'time'  => ['nullable', 'date_format:H:i'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        Appointment::create([
            'user_id' => Auth::id(),
            'title'   => $request->title,
            'date'    => $request->date,
            'time'    => $request->time,
            'notes'   => $request->notes,
        ]);

        return back()->with('success', 'Appointment saved ✅');
    }

    public function destroy(Appointment $appointment)
    {
        // Safety: only allow deleting your own
        abort_unless($appointment->user_id === Auth::id(), 403);

        $appointment->delete();

        return back()->with('success', 'Appointment deleted ✅');
    }
}
