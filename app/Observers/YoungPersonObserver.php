<?php

namespace App\Observers;
use App\Models\User;        
use App\Models\CaseFile;
class YoungPersonObserver
{
public function created(User $user): void
{
    if ($user->role === 'young_person') {
        CaseFile::create([
            'young_person_id' => $user->id,
            'status'          => 'open',
            'assigned_worker_id' => null, // or assign default worker
        ]);
    }
}
}
