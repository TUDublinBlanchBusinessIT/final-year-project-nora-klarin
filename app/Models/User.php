<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'username'];

public const ROLE_SOCIAL_WORKER = 'social_worker';
public const ROLE_CARER = 'carer';
public const ROLE_YOUNG_PERSON = 'young_person';

public function cases()
{
    return $this->belongsToMany(
        CaseFile::class,
        'case_user',
        'user_id',
        'case_file_id'
    )->withPivot('role', 'assigned_at')
     ->withTimestamps();
}


public function socialWorkerCases()
{
    return $this->cases()->wherePivot('role', 'social_worker');
}

public function carerCases()
{
    return $this->cases()->wherePivot('role', 'carer');
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



}



