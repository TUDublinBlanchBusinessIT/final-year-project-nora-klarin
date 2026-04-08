<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

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

    public function youngPerson()
    {
        return $this->belongsTo(User::class, 'youngpersonid', 'id');
    }

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

    public function goals()
    {
        return $this->hasMany(CaseGoal::class, 'caseid');
    }

    public function tasks()
    {
        return $this->hasManyThrough(
            Task::class,
            CaseGoal::class,
            'caseid',
            'goalid',
            'id',
            'goalid'
        );
    }

    public function timeline()
    {
        $events = collect();

        foreach ($this->documents as $doc) {
            $events->push([
                'date' => $doc->created_at,
                'title' => 'Document uploaded',
                'description' => $doc->name ?? $doc->title ?? 'A document was uploaded',
                'icon' => 'document',
                'user' => $doc->uploadedBy->name ?? 'User',
            ]);
        }

        foreach ($this->wellbeingChecks as $check) {
            $events->push([
                'date' => $check->created_at,
                'title' => 'Wellbeing check submitted',
                'description' => 'Overall score: ' . round($check->overall_score ?? 0, 1),
                'icon' => 'wellbeing',
                'user' => $check->submittedBy->name ?? 'Carer',
            ]);
        }

        foreach ($this->appointments as $appointment) {
            $events->push([
                'date' => $appointment->created_at,
                'title' => 'Appointment scheduled',
                'description' => ($appointment->location ?? 'No location set') . ' | ' .
                    Carbon::parse($appointment->start_time)->format('d M Y H:i'),
                'icon' => 'appointment',
                'user' => $appointment->creator->name ?? 'User',
            ]);
        }

        foreach ($this->placements as $placement) {
            $events->push([
                'date' => $placement->created_at,
                'title' => 'Placement updated',
                'description' => $placement->notes ?? 'Placement information was updated',
                'icon' => 'placement',
                'user' => $placement->placement->carer->name ?? 'Social Worker',
            ]);
        }

        // foreach ($this->alerts as $alert) {
        //     $events->push([
        //         'date' => $alert->created_at,
        //         'title' => 'Alert created',
        //         'description' => $alert->message ?? 'A case alert was created',
        //         'icon' => 'alert',
        //         'user' => 'System',
        //     ]);
        // }

        return $events->sortByDesc('date')->values();
    }
}