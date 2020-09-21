<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PlanFeatureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'plan_features';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "planId"                                => $this["plan_id"],
            "name"                                  => $this["name"],
            "check"                                 => $this["check"],
            "info"                                  => $this["info"],
            "included"                              => $this["included"],
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
