<?php
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['goalid', 'description', 'status', 'due_date'];

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goalid');
    }
}
