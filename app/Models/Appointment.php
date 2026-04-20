<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Appointment extends Model
{
    protected $table = 'appointments'; // match your table
    public $timestamps = false;       // your table uses createdat

    protected $fillable = [
        'case_file_id',
        'young_person_id',
        'start_time',
        'end_time',
        'title',
        'location',
        'notes',
        'created_by',
    ];

    // Link to the case
    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    // Carers assigned to this appointment
public function carers()
{
    return $this->belongsToMany(
        \App\Models\User::class,
        'appointment_user',
        'appointment_id',
        'user_id'
    );
}


    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'young_person_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper: next appointment for a case
    public static function nextForCase($caseId)
    {
        return self::where('caseid', $caseId)
                   ->where('starttime', '>=', now())
                   ->orderBy('starttime', 'asc')
                   ->first();
    }

public static function nextAvailableSlot($socialWorkerId, Carbon $desiredDate, $durationMinutes = 30)
{
    $start = $desiredDate->copy()->setHour(9)->setMinute(0);
    $end   = $desiredDate->copy()->setHour(17)->setMinute(0);

    $appointments = self::where('created_by', $socialWorkerId)
                        ->whereDate('start_time', $desiredDate->toDateString())
                        ->orderBy('start_time')
                        ->get();

    while ($start->lt($end)) {
        $slotEnd = $start->copy()->addMinutes($durationMinutes);

        $overlap = $appointments->first(function($a) use ($start, $slotEnd) {
            return $a->start_time < $slotEnd && $a->end_time > $start;
        });

        if (!$overlap) {
            return $start;
        }

        $start->addMinutes(30);
    }

    return null;
}
}
