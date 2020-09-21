<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SocialMediaLinkResource extends JsonResource
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
            "name"                      => $this["name"],
            "socialMediaLink"           => $this["social_media_link"],
            'createdAt'                 => $this["created_at"],
            "updatedAt"                 => $this["updated_at"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
