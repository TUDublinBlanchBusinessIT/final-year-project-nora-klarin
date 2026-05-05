<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    protected $fillable = [
        'case_file_id',
        'carer_id',
        'type',
        'location',      // schema column is 'location', not 'address'
        'notes',
        'start_date',
        'end_date',
        'capacity',
        'current_occupancy',
        'status',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude'   => 'float',
        'longitude'  => 'float',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }
}