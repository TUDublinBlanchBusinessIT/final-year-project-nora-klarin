<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CaseFile extends Model

{

    protected $table = 'case_files';

    protected $fillable = [
        'case_reference',
        'young_person_id',
        'placement_type',
        'placement_location',
        'risk_level',
        'opened_at',
        'closed_at',
        'status',
        'summary',
        'last_reviewed_at',

    ];

        protected static function boot()
    {
        parent::boot();
 
        static::creating(function ($case) {
            if (empty($case->case_reference)) {
                $case->case_reference = self::generateCaseReference();
            }
        });
    }

    public function users()

    {

        return $this->belongsToMany(

            User::class,
            'case_user',
            'case_file_id',
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
        return $this->belongsTo(User::class, 'young_person_id', 'id');
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



    public function suggestedFollowUp()

    {

        $riskIntervals = [

            'high' => 7,

            'medium' => 14,

            'low' => 56,

        ];



        $lastAppointment = $this->appointments()->orderByDesc('start_time')->first();

        $startDate = $lastAppointment ? $lastAppointment->start_time : $this->created_at;



        $riskLevel = strtolower($this->risklevel ?? $this->risk_level ?? 'medium');

        $intervalDays = $riskIntervals[$riskLevel] ?? 28;



        return Carbon::parse($startDate)->addDays($intervalDays);

    }

       public static function generateCaseReference(): string
    {
        $year = now()->year;
 
        $lastCase = self::whereYear('created_at', $year)
            ->whereNotNull('case_reference')
            ->where('case_reference', 'like', "CF-{$year}-%")
            ->orderByDesc('id')
            ->first();
 
        $number = 1;
 
        if ($lastCase && preg_match('/CF-\d{4}-(\d+)$/', $lastCase->case_reference, $matches)) {
            $number = intval($matches[1]) + 1;
        }
 
        return 'CF-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
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

                'user' => $doc->uploader->name ?? 'User',

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

                'date' => $appointment->created_at ?? $appointment->start_time,

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

                'user' => $placement->carer->name ?? 'Social Worker',

            ]);

        }



        return $events->sortByDesc('date')->values();

    }
    
    public function markReviewed(\App\Models\CaseFile $case)
    {
     abort_if(!$case->users()->where('users.id', auth()->id())->exists(), 403);
     $case->update(['last_reviewed_at' => now()]);
     return redirect()->back()->with('success', 'Case marked as reviewed.');
    }
}