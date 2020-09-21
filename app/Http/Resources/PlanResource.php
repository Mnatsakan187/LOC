<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'plans';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "name"                                  => $this["name"],
            "price"                                 => $this["price"],
            "features"                              => PlanFeatureResource::collection($this->planFeature),
            "createdAt"                             => $this["created_at"],
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
