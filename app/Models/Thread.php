<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class Thread extends Model

{

    use HasFactory;



    protected $fillable = [

        'child_id',

        'carer_id',

        'social_worker_id',

        'conversation_type',

    ];



    public function child()

    {

        return $this->belongsTo(User::class, 'child_id');

    }



    public function carer()

    {

        return $this->belongsTo(User::class, 'carer_id');

    }



    public function socialWorker()

    {

        return $this->belongsTo(User::class, 'social_worker_id');

    }



    public function messages()

    {

        return $this->hasMany(Message::class, 'thread_id');

    }

}

