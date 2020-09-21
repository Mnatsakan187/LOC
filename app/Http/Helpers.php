<?php

namespace App\Http;


use App\Event;
use App\Message;
use App\Notification;
use App\Poll;
use App\Post;
use App\Profile;
use App\Project;
use App\User;
use Illuminate\Support\Facades\Auth;

class Helpers
{
    public static function storeNotification($userId, $type, $summary, $notificationableId, $profileId, $name)
    {

        if($summary == 'project') {
            /** @var Project $notificationable */
            $notificationable = Project::find($notificationableId);
        }elseif ($summary == 'event'){
            $notificationable = Event::find($notificationableId);
        }elseif ($summary == 'post'){
            $notificationable = Post::find($notificationableId);
        }elseif ($summary == 'poll'){
            $notificationable = Poll::find($notificationableId);
        }elseif ($summary == 'user'){
            $notificationable = User::find($notificationableId);
        }elseif ($summary == 'accept') {
            $notificationable = User::find($notificationableId);
        }elseif ($summary == 'profile'){
            $notificationable = Profile::find($notificationableId);
        }elseif ($summary == 'message'){
            $notificationable = Message::find($notificationableId);
        }

        /** @var Notification $notification */
        $notification = Notification::create([
            'user_id'           => $userId,
            'summary'           => $summary,
            'created_by'        => Auth::user()->id,
            'type'              => $type,
            'profile_id'        => $profileId,
            'is_read'           => 0,
            'name'           => $name
        ]);


        $notificationable->notifications()->attach($notification->getKey());

        return $notification->getKey();
    }
}
