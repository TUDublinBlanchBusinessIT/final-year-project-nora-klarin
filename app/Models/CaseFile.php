<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use App\Models\User;



class CaseFile extends Model

{

    protected $table = 'case_files';



    protected $fillable = [

        'youngpersonid',

        'risklevel',

        'openedat',

        'status'

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



    // Young person is stored in users table

    public function youngPerson()

    {

        return $this->belongsTo(User::class, 'youngpersonid', 'id');

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

        return $this->hasManyThrough(

            Goal::class,

            CaseGoal::class,

            'caseid',

            'id',

            'id',

            'goalid'

        );

    }



    public function tasks()

    {

        return $this->hasManyThrough(

            Task::class,

            TaskGoal::class,

            'goalid',

            'id',

            'id',

            'taskid'

        );

    }

}