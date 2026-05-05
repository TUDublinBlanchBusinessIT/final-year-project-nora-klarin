<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
   public function markRead(string $id)
    {
        $notif = auth()->user()->notifications()->findOrFail($id);
        $notif->markAsRead();
        return back();
    }

    public function markAllRead()
    {
            auth()->user()->unreadNotifications->markAsRead();
            return back();
    }
}
