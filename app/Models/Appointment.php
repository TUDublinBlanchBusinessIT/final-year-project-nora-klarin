<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Appointment extends Model
{
    protected $table = 'appointments';
    public $timestamps = false;      

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

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

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
    protected $casts = [
        'date' => 'date',
    ];


    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'appointment_user',
            'appointment_id',
            'user_id'
        );
    }
}
