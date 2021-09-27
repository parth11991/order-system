<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\Image;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    
    public function index()
    {

    }

    /**
     * @param  int  $id  Notification ID
     * @return \Illuminate\Http\Response
     */
    public function notificationMarkAsRead($id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();
        if ($notification) {
            $notification->markAsRead();
            //return redirect($notification->data['actionUrl']);
            return back();
        }
    }

    public function notificationMarkAllAsRead($userID)
    {
        $user = User::findOrFail($userID);
        $user->unreadNotifications->map(function($n) {
            $n->markAsRead();
        });
        return back();
    }

    public static function timeDiff($created_at)
    {
        $updated = new Carbon($created_at);
        $now = Carbon::now();
        if ($updated->diffInMinutes($now) < 1) {
            $notification_time = $updated->diffInSeconds($now). " seconds ago";
        } elseif ($updated->diffInHours($now) < 1) {
            $notification_time = $updated->diffInMinutes($now) > 1 ? sprintf("%d minutes ago", $updated->diffInMinutes($now)) : sprintf("%d minute ago", $updated->diffInMinutes($now));
        } elseif ($updated->diffInDays($now) < 1) {
            $notification_time = $updated->diffInHours($now) > 1 ? sprintf("%d hours ago", $updated->diffInHours($now)) : sprintf("%d hour ago", $updated->diffInHours($now));
        } elseif ($updated->diffInWeeks($now) < 7) {
            $notification_time = $updated->diffInDays($now) > 1 ? sprintf("%d days ago", $updated->diffInDays($now)) : sprintf("%d day ago", $updated->diffInDays($now));
        } elseif ($updated->diffInMonths($now) < 1) {
            $notification_time = $updated->diffInWeeks($now) > 1 ? sprintf("%d weeks ago", $updated->diffInWeeks($now)) : sprintf("%d week ago", $updated->diffInWeeks($now));
        } elseif ($updated->diffInYears($now) < 1) {
            $notification_time = $updated->diffInMonths($now) > 1 ? sprintf("%d months ago", $updated->diffInMonths($now)) : sprintf("%d month ago", $updated->diffInMonths($now));
        } else {
            $notification_time = $updated->diffInYears($now) > 1 ? sprintf("%d years ago", $updated->diffInYears($now)) : sprintf("%d year ago", $updated->diffInYears($now));
        }

        return $notification_time;
    }

}