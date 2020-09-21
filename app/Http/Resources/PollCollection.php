<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PollCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\PollResource';

    public function toArray($request)
    {
        return [
            'meta' => [
                'self' => '/polls',
                'count' => $this->collection->count()
            ],
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
        ];
    }
}
