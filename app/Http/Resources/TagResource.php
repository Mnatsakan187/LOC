<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TagResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'tags';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "name"                                  => $this["name"],
            "description"                           => $this["description"],
            "rbgColorCode"                          => $this["rbg_color_code"],
            'createdBy'                             => $this["created_by"],
            'createdAt'                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
