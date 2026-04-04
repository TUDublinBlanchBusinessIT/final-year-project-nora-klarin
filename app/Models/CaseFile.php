<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CaseFile extends Model
{
    protected $table = 'case_files';

    protected $fillable = [
        'case_reference',
        'youngpersonid',
        'risklevel',
        'openedat',
        'status',
    ];

    // Users linked to case (carers + social workers)
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

    public function carers()
    {
        return $this->users()->wherePivot('role', 'carer');
    }

    // Young person linked to case
    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'youngpersonid', 'id');
    }

    // Related case data
    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'case_file_id');
    }

    public function placements()
    {
        return $this->hasMany(Placement::class, 'case_file_id');
    }

    public function medicalInfos()
    {
        return $this->hasMany(MedicalInfo::class, 'case_file_id');
    }

    public function educationInfos()
    {
        return $this->hasMany(EducationInfo::class, 'case_file_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'case_file_id');
    }

    public function wellbeingChecks()
    {
        return $this->hasMany(WellbeingCheck::class, 'case_file_id');
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class, 'caseid');
    }

    // Goals linked through case_goals table
    public function goals()
    {
        return $this->hasMany(CaseGoal::class, 'caseid');
    }

    // Tasks through goals
    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,
            CaseGoal::class,
            'caseid',   // Foreign key on CaseGoal table
            'goalid',   // Foreign key on Task table
            'id',       // Local key on CaseFile
            'goalid'    // Local key on CaseGoal
        );
    }
}