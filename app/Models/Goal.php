<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['caseid', 'title', 'description', 'status', 'origin'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'caseid');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'goalid');
    }
}
