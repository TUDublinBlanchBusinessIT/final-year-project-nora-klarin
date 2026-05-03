<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['caseid', 'title', 'description', 'status', 'origin'];

    public function caseFile()
    {
        return $this->belongsTo(CaseFile::class, 'case_file_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'goal_id');
    }

    public function sourceDomain()
    {
        return $this->belongsTo(\App\Models\Domain::class, 'source_domain_id');
    }
}
