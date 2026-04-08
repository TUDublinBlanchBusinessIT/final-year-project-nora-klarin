<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CasePlacement extends Model
{
    protected $table = 'case_placements';

    protected $fillable = [
        'case_file_id', 'placement_id', 'start_date', 'end_date', 'assigned_by', 'notes'
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function placement()
    {
        return $this->belongsTo(Placement::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}