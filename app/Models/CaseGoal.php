<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseGoal extends Model
{
    protected $table = 'case_goals';

    protected $fillable = [
        'case_file_id',
        'goal_id',
        'status',
        'child_visible',
        'child_accepted_at',
        'due_date',
        'suggested_by',
        'source_check_id',
    ];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function goal()
    {
        return $this->belongsTo(\App\Models\Goal::class);
    }

    public function tasks()
    {
        return $this->hasMany(CaseTask::class, 'case_goal_id');
    }
}
