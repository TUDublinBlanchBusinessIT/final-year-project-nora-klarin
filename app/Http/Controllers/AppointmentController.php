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
            'case_file_id' => ['required', 'exists:case_files,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->time);

        $endTime = $request->filled('end_time')
            ? Carbon::parse($request->date . ' ' . $request->end_time)
            : $startTime->copy()->addMinutes(30);

        $appointment = Appointment::create([
            'case_file_id' => $request->case_file_id,
            'title' => $request->title,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'location' => $request->location,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        if ($request->filled('user_ids')) {
            $appointment->users()->sync($request->user_ids);
        }

        return back()->with('success', 'Appointment saved ✅');
    }

    public function destroy(Appointment $appointment)
    {
        // Only allow the creator to delete
        abort_unless((int) $appointment->created_by === (int) Auth::id(), 403);

        $appointment->users()->detach();
        $appointment->delete();

        return back()->with('success', 'Appointment deleted ✅');
    }
}