<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'comments';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "commentText"                           => $this["comment_text"],
            'createdAt'                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"],
            'user'                                  => new UserResource($this->user),
            'replies'                               =>  ReplyResource::collection($this->replies)
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
