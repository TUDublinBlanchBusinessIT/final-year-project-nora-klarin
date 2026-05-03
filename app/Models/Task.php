<?php
class Task extends Model
{
    use HasFactory;

    protected $fillable = ['goal_id', 'description', 'status', 'due_date'];

    public function goal()
    {
        return $this->belongsTo(Goal::class, 'goal_id');
    }
}
