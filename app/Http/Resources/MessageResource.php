<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'messages';

    public function toArray($request)
    {
        return [
            "id"                        => $this["id"],
            "fromUserId"                => $this["from_user_id"],
            "toUserId"                  => $this["to_user_id"],
            "message"                   => $this["message"],
            "summary"                   => $this["summary"],
            "isRead"                    => $this["isRead"],
            "fromUser"                  =>  new UserResource($this->fromUser),
            "toUser"                    =>  new UserResource($this->toUser),
            'createdAt'                 => $this["created_at"],
            "updatedAt"                 => $this["updated_at"],
            "deletedAt"                 => $this["deleted_at"],
        ];
    }


    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
