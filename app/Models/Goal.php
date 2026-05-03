<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Goal extends Model
{
    use HasFactory;

    protected $fillable = ['title',
        'description',
        'source_domain_id',
        'approved_by',
        'approved_at',
        'suggested_at',];

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
