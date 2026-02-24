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

 public function users()
{
    return $this->belongsToMany(
        User::class,
        'case_user',
        'case_file_id',    
        'user_id'    
    )->withPivot('role', 'assigned_at')
    ->withTimestamps();
}


    public function socialWorkers()
    {
        return $this->users()->wherePivot('role', 'social_worker');
    }

    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'young_person_id');
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


    public function alerts()
    {
        return $this->hasMany(Alert::class, 'case_file_id');
    }

    public function goals()
    {
        return $this->hasManyThrough(
            Goal::class,
            CaseGoal::class,
            'case_file_id',   // Foreign key on case_goal table
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

        public function medicalInfos() { return $this->hasMany(MedicalInfo::class); }
        
        public function educationInfos() { return $this->hasMany(EducationInfo::class); }

        public function documents() { return $this->hasMany(CaseDocument::class); }
}



