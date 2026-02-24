<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    protected $fillable = [
        'type', 'address', 'capacity', 'current_occupancy',
        'status', 'carer_id', 'latitude', 'longitude', 'notes'
    ];

    public function carer()
    {
        return $this->belongsTo(User::class, 'carer_id');
    }

    public function carers()
    {
        return $this->belongsToMany(User::class, 'placement_carer', 'placement_id', 'user_id');
    }

    public function casePlacements()
    {
        return $this->hasMany(CasePlacement::class);
    }

    public function cases()
    {
        return $this->belongsToMany(CaseFile::class, 'case_placements')
                    ->withPivot('start_date','end_date','assigned_by','notes')
                    ->withTimestamps();
    }
}