<?php

namespace App\Http\Controllers;

use App\Notification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function mark_all_notifications_as_read(Request $req)
    {
        auth()->user()->notifications->each(function ($notification) {
            $notification->update(['read' => true]);
        });

        return redirect('/');
    }

    public function go_to_notification(Request $req, Notification $notification)
    {
        if ($notification->to_user_id === auth()->user()->id) {
            $notification->update(['read' => true]);
            return redirect()->to($notification->link);
        } else abort(403);
    }
    //
}
