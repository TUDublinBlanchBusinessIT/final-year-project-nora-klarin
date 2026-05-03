<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseFile;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class SocialWorkerAppointmentController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $appointments = $user->socialWorkerAppointments()->with([
            'youngPerson',
            'caseFile'
            
            ])->orderBy('start_time')->get();

        return view('socialworker.appointments.index', compact('appointments'));
    }
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
        'date' => 'required|date',
        'time' => 'required|date_format:H:i',
        'end_time' => 'nullable|date_format:H:i',
        'location' => 'nullable|string',
        'title' => 'required|string',
        'description' => 'nullable|string',
        'invite_child' => 'nullable|boolean',
        'carers' => 'nullable|array',
        'carers.*' => 'exists:users,id',
    ]);

    $case = CaseFile::findOrFail($data['case_file_id']);

    $startTime = Carbon::parse($data['date'] . ' ' . $data['time']);

    $endTime = $request->filled('end_time')
        ? Carbon::parse($data['date'] . ' ' . $data['end_time'])
        : $startTime->copy()->addMinutes(30);

    $appointment = Appointment::create([
        'case_file_id' => $data['case_file_id'],
        'created_by'   => auth()->id(),
        'start_time'   => $startTime,
        'end_time'     => $endTime,
        'location'     => $data['location'] ?? null,
        'title'        => $data['title'],
        'description'  => $data['description'] ?? null,
    ]);

    // Attach attendees via pivot
    $attendees = [];
    if ($data['invite_child']) {
        $attendees[] = $case->young_person_id;
    }
    if (!empty($data['carers'])) {
        $attendees = array_merge($attendees, $data['carers']);
    }
    if (!empty($attendees)) {
        $appointment->users()->attach($attendees);
    }

    return redirect()
        ->route('socialworker.cases.show', $data['case_file_id'])
        ->with('success', 'Appointment created successfully');
}
}
