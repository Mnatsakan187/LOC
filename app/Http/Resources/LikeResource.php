<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LikeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'likes';

    public function toArray($request)
    {
        return [
            "id"                        => $this["id"],
            "userId"                    => $this["user_id"],
            "likedDate"                 => $this["liked_date"],
            'createdAt'                 => $this["created_at"],
            "updatedAt"                 => $this["updated_at"],
            "likeableId"                => isset($this->projects()->first()->id) ? $this->projects()->first()->id : null,
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
