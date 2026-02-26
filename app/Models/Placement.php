<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Placement extends Model
{
    protected $fillable = [
        'carer_id',
        'child_id',
        'start_date',
        'end_date',
        'status'
    ];

    public function child()
    {
        return $this->belongsTo(Child::class);
    }
}