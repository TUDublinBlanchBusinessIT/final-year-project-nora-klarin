<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    protected $table = 'case_files';

    protected $fillable = [
        'young_person_id',
        'status',
        'risk_level',
        'placement_type',
        'placement_location',
        'opened_at',
        'closed_at',
        'last_reviewed_at',
        'summary',
    ];

    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'young_person_id');
    }
}