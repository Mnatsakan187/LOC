<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'groups';

    public function toArray($request)
    {

        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "name"                                  => $this["name"],
            "description"                           => $this["description"],
            "createdBy"                             => $this["created_by"],
            "updatedBy"                             => $this["updated_by"],
            "isVisible"                             => $this["is_visible"],
            "createdAt"                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"],
            "members"                               => UserResource::collection($this->users()->orderBy('id', 'desc')->get()),
            "posts"                                 => PostResource::collection($this->posts()->orderBy('id', 'desc')->get()),
            "user"                                  => [],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
