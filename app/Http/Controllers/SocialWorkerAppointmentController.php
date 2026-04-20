<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class SocialWorkerAppointmentController extends Controller
{
    public function create(CaseFile $case)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $youngPerson = $case->youngPerson;

        $carers = $case->carers;

    $suggestedDate = $case->suggestedFollowUp();
    $availableSlot = Appointment::nextAvailableSlot(auth()->id(), Carbon::parse($suggestedDate));

    return view('socialworker.appointments.create', compact(
        'case',
        'youngPerson',
        'carers',
        'availableSlot'
    ));
    }

public function store(Request $request)
{
    abort_if(auth()->user()->role !== 'social_worker', 403);

    $data = $request->validate([
        'case_file_id' => 'required|exists:case_files,id',
        'start_time'   => 'required|date',
        'end_time'     => 'required|date|after:start_time',
        'location'     => 'nullable|string',
        'title'        => 'required|string',
        'description'  => 'nullable|string',
        'carers'       => 'nullable|array',
        'carers.*'     => 'exists:users,id',
    ]);

    $appointment = Appointment::create([
        'case_file_id' => $data['case_file_id'],
        'created_by'   => auth()->id(),
        'start_time'   => $data['start_time'],
        'end_time'     => $data['end_time'],
        'location'     => $data['location'] ?? null,
        'title'        => $data['title'],
        'description'  => $data['description'] ?? null,
    ]);

    // Attach carers via pivot
    if (!empty($data['carers'])) {
        $appointment->carers()->attach($data['carers']);
    }

    return redirect()
        ->route('socialworker.case.show', $data['case_file_id'])
        ->with('success', 'Appointment created successfully');
}
}
