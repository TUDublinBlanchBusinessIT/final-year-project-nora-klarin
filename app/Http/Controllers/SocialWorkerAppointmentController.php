<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Http\Request;

class SocialWorkerAppointmentController extends Controller
{
    public function create(CaseFile $case)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $youngPerson = $case->youngPerson;

        $carers = $case->carers;

        return view('socialworker.appointments.create', compact(
            'case',
            'youngPerson',
            'carers'
        ));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'social_worker', 403);

        $data = $request->validate([
            'case_file_id'   => 'required|exists:case_files,id',
            'young_person_id'=> 'required|exists:users,id',
            'start_time'     => 'required|date',
            'end_time'       => 'nullable|date|after:start_time',
            'location'       => 'nullable|string',
            'notes'          => 'nullable|string',
            'carers'         => 'nullable|array',
            'carers.*'       => 'exists:users,id',
        ]);

        $appointment = Appointment::create([
            'case_file_id'    => $data['case_file_id'],
            'created_by'      => auth()->id(),
            'young_person_id' => $data['young_person_id'],
            'start_time'      => $data['start_time'],
            'end_time'        => $data['end_time'] ?? null,
            'location'        => $data['location'] ?? null,
            'notes'           => $data['notes'] ?? null,
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
