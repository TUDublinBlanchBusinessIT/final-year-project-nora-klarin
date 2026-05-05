<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markRead(string $id)
    {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        $data = $notif->data;
        $caseId = $data['case_file_id'] ?? null;

        if ($caseId) {
            $user = auth()->user();
            if ($user->role === 'social_worker') {
                return redirect()->route('socialworker.cases.show', $caseId);
            }
            if ($user->role === 'carer') {
                return redirect()->route('carer.cases.show', $caseId);
            }
        }

        return back();
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
