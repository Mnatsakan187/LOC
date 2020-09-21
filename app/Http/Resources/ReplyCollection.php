<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReplyCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\ReplyResource';

    public function toArray($request)
    {
        return [
            'meta' => [
                'self' => '/replies',
                'count' => $this->collection->count()
            ],
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
        ];
    }
}
