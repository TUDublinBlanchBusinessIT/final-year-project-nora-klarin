<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class CaseFile extends Model
{
    protected $table = 'case_files';

    protected $fillable = ['youngpersonid', 'risklevel', 'openedat', 'status'];

 public function users()
{
    return $this->belongsToMany(
        User::class,
        'case_user',
        'case_id',    
        'user_id'    
    )->withPivot('role', 'assigned_at');
}


    public function socialWorkers()
    {
        return $this->users()->wherePivot('role', 'social_worker');
    }

    public function youngPerson()
    {
        return $this->belongsTo(YoungPerson::class, 'young_person_id');
    }

    public function carers()
    {
        return $this->users()->wherePivot('role', 'carer');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'case_file_id');
    }

    public function placements()
    {
        return $this->hasMany(Placement::class, 'case_file_id');
    }

    // Wellbeing checks
    public function wellbeingChecks()
    {
        return $this->hasMany(WellbeingCheck::class, 'youngpersonid', 'youngpersonid');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'caseid');
    }

    public function goals()
    {
        return $this->hasManyThrough(
            Goal::class,
            CaseGoal::class,
            'caseid',   // Foreign key on case_goal table
            'id',       // Foreign key on goals table
            'id',       // Local key on case table
            'goalid'    // Local key on case_goal table
        );
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,
            TaskGoal::class,
            'goalid',  // foreign key on taskgoal table
            'id',      // foreign key on tasks table
            'id',      // local key on case table
            'taskid'   // local key on taskgoal table
        );
        }
}



