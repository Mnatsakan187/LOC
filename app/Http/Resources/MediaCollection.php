<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MediaCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\MediaResource';

    public function toArray($request)
    {
        return [
            'meta' => [
                'self' => '/medium',
                'count' => $this->collection->count()
            ],
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
        ];
    }
}
