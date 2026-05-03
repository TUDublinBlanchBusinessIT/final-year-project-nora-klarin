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
