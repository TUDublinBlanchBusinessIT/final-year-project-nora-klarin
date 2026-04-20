<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'username',
        'dob',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

        protected $casts = [
        'dob' => 'date',
    ];

    public function cases()
    {
        return $this->belongsToMany(
            CaseFile::class,
            'case_user',
            'user_id',
            'case_id'
        )->withPivot('role', 'assigned_at');
    }

    public function caseFile()
    {
        return $this->hasOne(CaseFile::class, 'youngpersonid', 'id');
    }

    public function socialWorkerCases()
    {
        return $this->belongsToMany(
            CaseFile::class,
            'case_user',
            'user_id',
            'case_id'
        )->withPivot('role', 'assigned_at')
         ->wherePivot('role', 'social_worker');
    }

    public function carerCases()
    {
        return $this->belongsToMany(
            CaseFile::class,
            'case_user',
            'user_id',
            'case_id'
        )->withPivot('role', 'assigned_at')
         ->wherePivot('role', 'carer');
    }

    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    public function appointmentsAsYoungPerson()
    {
        return $this->hasMany(Appointment::class, 'young_person_id');
    }

public function appointments()
{
    return $this->belongsToMany(
        \App\Models\Appointment::class,  
        'appointment_user',              
        'user_id',                       
        'appointment_id'                 
    );
}

public function wellbeingChecks()
{
    return $this->hasMany(\App\Models\WellbeingCheck::class, 'young_person_id');
}

public function socialWorkerAppointments()
{
    return $this->hasMany(\App\Models\Appointment::class, 'created_by');
}

}



