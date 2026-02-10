<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseGoal extends Model
{
    protected $table = 'case_goals';

    protected $fillable = [
        'case_id',
        'title',
        'description',
        'status',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }
    public function goals()
{
    return $this->hasMany(CaseGoal::class, 'case_id');
}
}
