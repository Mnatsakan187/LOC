<?php

namespace App\Http\Resources;

use App\Notification;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'notifications';

    public function toArray($request)
    {
        $unreadNotificationsCount = Notification::where('user_id', Auth::user()->id)->where('is_read', 0)->count();

        return [
            "id"                 => $this["id"],
            "userId"             => $this["user_id"],
            "summary"            => $this["summary"],
            "createdBy"          => $this["created_by"],
            "isRead"             => $this["is_read"],
            "readDate"           => $this["read_date"],
            "name"               => $this["name"],
            "event"              => new EventResource($this->events()->first()),
            "project"            => new ProjectResource($this->projects()->first()),
            "post"               => new PostResource($this->posts()->first()),
            "poll"               => new PollResource($this->polls()->first()),
            "type"               => $this["type"],
            "notificationUser"   => new UserResource($this->user),
            "profile"            => new ProfileResource($this->profile),
            'notificationCount'  => $unreadNotificationsCount,
            'showNotificationCount' => $unreadNotificationsCount > 0,
            'createdAt'          => $this["created_at"],
            "updatedAt"          => $this["updated_at"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
