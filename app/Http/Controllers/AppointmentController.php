<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CaseFile;
use App\Models\User;
use App\Notifications\CareHubNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'case_file_id' => ['required', 'exists:case_files,id'],
            'title'        => ['required', 'string', 'max:255'],
            'date'         => ['required', 'date'],
            'time'         => ['required', 'date_format:H:i'],
            'end_time'     => ['nullable', 'date_format:H:i'],
            'location'     => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string', 'max:2000'],
            'user_ids'     => ['nullable', 'array'],
            'user_ids.*'   => ['exists:users,id'],
        ]);

        $startTime = Carbon::parse($request->date . ' ' . $request->time);
        $endTime   = $request->filled('end_time')
            ? Carbon::parse($request->date . ' ' . $request->end_time)
            : $startTime->copy()->addMinutes(30);

        $appointment = Appointment::create([
            'case_file_id' => $request->case_file_id,
            'title'        => $request->title,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'location'     => $request->location,
            'notes'        => $request->notes,
            'created_by'   => Auth::id(),
        ]);

        $caseFile      = CaseFile::find($request->case_file_id);
        $youngPersonId = $caseFile->young_person_id;

        $allAttendees = collect($request->input('user_ids', []))
            ->push($youngPersonId)
            ->push(Auth::id())
            ->unique()
            ->filter()
            ->values()
            ->all();

        $appointment->users()->sync($allAttendees);

        // Notify assigned carers on this case
        $this->notifyCarers(
            caseFile:    $caseFile,
            type:        'appointment_created',
            summary:     'New appointment: ' . $appointment->title . ' on ' . $startTime->format('d M Y g:i A'),
            caseFileId:  $caseFile->id,
            excludeId:   Auth::id(),
        );

        return back()->with('success', 'Appointment saved');
    }

    public function destroy(Appointment $appointment)
    {
        abort_unless((int) $appointment->created_by === (int) Auth::id(), 403);

        $caseFile = CaseFile::find($appointment->case_file_id);
        $title    = $appointment->title;
        $date     = Carbon::parse($appointment->start_time)->format('d M Y g:i A');

        $appointment->users()->detach();
        $appointment->delete();

        if ($caseFile) {
            $this->notifyCarers(
                caseFile:   $caseFile,
                type:       'appointment_cancelled',
                summary:    'Appointment cancelled: ' . $title . ' (' . $date . ')',
                caseFileId: $caseFile->id,
                excludeId:  Auth::id(),
            );
        }

        return back()->with('success', 'Appointment deleted');
    }

    private function notifyCarers(
        CaseFile $caseFile,
        string $type,
        string $summary,
        int $caseFileId,
        ?int $excludeId = null,
    ): void {
        User::whereIn('id',
            \Illuminate\Support\Facades\DB::table('case_user')
                ->where('case_file_id', $caseFile->id)
                ->where('role', 'carer')
                ->pluck('user_id')
        )->get()
         ->reject(fn($u) => $u->id === $excludeId)
         ->each(fn($carer) => $carer->notify(new CareHubNotification(
             type:    $type,
             summary: $summary,
             data:    ['case_file_id' => $caseFileId],
         )));
    }
}