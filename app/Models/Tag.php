<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;



class Tag extends Model

{

    use HasFactory;



    protected $fillable = [

        'name',

        'category',

        'alert_override',

        'alert_threshold',

    ];



    public function questions()

    {

        return $this->belongsToMany(Question::class, 'question_tag');

    }



    public function goalTemplates()

    {

        return $this->belongsToMany(GoalTemplate::class, 'tag_goal_templates')

            ->withPivot('trigger_threshold')

            ->withTimestamps();

    }

}