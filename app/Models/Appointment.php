<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        return $this->belongsToMany(User::class);
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
}
