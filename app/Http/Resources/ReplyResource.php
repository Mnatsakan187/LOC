<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReplyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'replies';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "commentId"                             => $this["comment_id"],
            "replyText"                             => $this["reply_text"],
            'createdAt'                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"],
            'user'                                  => new UserResource($this->user),
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
