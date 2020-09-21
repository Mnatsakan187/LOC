<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PlanFeatureCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\PlanFeatureResource';

    public function toArray($request)
    {
        return [
            'meta' => [
                'self' => '/plan_features',
                'count' => $this->collection->count()
            ],
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
        ];
    }
}
