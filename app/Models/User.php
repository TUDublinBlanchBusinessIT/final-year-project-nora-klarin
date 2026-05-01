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
        'username',
        'email',
        'password',
        'role',
        'dob',
        'carer_id',
        'login_code',
        'login_code_expires_at',
        'theme',
        'chatbot_name',
        'dashboard_layout',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

        protected $casts = [
        'dob' => 'date',
        'last_login_at' => 'datetime',
    ];

    public function age(): ?int
    {
        return $this->dob?->age;
    }

    public function isYoungChild(): bool
    {
        return $this->role === 'young_person' && $this->age() !== null && $this->age() < 10;
    }

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

protected static function booted()
{
    static::created(function ($user) {
        if ($user->role === 'young_person') {
            \App\Models\CaseFile::create([
                'young_person_id' => $user->id,
                'status' => 'open',
                'case_reference' => \App\Models\CaseFile::generateCaseReference(),
                'opened_at' => now(),
            ]);
        }
    });
}

}
