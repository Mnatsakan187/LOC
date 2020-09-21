<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'medium';

    public function toArray($request)
    {
        return [
            "id"                        => $this["id"],
            "userId"                    => $this["user_id"],
            "displayName"               => $this["display_name"],
            "fieldName"                 => $this["field_name"],
            "uri"                       => $this["uri"],
            'createdBy'                 => $this["created_by"],
            'updatedBy'                 => $this["updated_by"],
            'mediaType'                 => $this["media_type"],
            'createdByImage'            => $this["created_by_image"],
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
