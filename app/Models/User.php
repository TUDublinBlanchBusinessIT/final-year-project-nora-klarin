<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;


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
            'case_file_id'
        )->withPivot('role', 'assigned_at');
    }

    public function caseFile()
    {
        return $this->hasOne(CaseFile::class, 'young_person_id', 'id');
    }

    public function socialWorkerCases()
    {
        return $this->belongsToMany(
            CaseFile::class,
            'case_user',
            'user_id',
            'case_file_id'
        )->withPivot('role', 'assigned_at')
         ->wherePivot('role', 'social_worker');
    }

    public function carerCases()
    {
        return $this->belongsToMany(
            CaseFile::class,
            'case_user',
            'user_id',
            'case_file_id'
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

    public static function canMessage(User $sender, User $recipient): bool
    {
        if ($sender->id === $recipient->id) return false;

        if ($sender->role === 'social_worker') {
            if ($recipient->role === 'social_worker') {
                return true; // can message other social workers
            }
            return in_array($recipient->role, ['carer', 'young_person'])
                && self::sharesCase($sender->id, $recipient->id);
        }

        if ($sender->role === 'carer') {
            return $recipient->role === 'social_worker'
                && self::sharesCase($sender->id, $recipient->id);
        }

        if ($sender->role === 'young_person') {
            return in_array($recipient->role, ['carer', 'social_worker'])
                && self::sharesCase($sender->id, $recipient->id);
        }

        return false;
    }

    private static function sharesCase(int $userA, int $userB): bool
    {
        return DB::table('case_user as cu1')
            ->join('case_user as cu2', 'cu1.case_file_id', '=', 'cu2.case_file_id')
            ->where('cu1.user_id', $userA)
            ->where('cu2.user_id', $userB)
            ->exists();
    }

    public static function messageableUsers(User $user)
    {
        return User::query()
            ->get()
            ->filter(fn ($other) => self::canMessage($user, $other))
            ->values();
    }
}
