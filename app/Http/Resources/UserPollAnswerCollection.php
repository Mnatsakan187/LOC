<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPollAnswerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $collects = 'App\Http\Resources\UserPollAnswerResource';

    public function toArray($request)
    {
        return [
            'meta' => [
                'self' => '/user-poll-answer',
                'count' => $this->collection->count()
            ],
            'data' => $this->collection
                ->map
                ->toArray($request)
                ->all(),
        ];
    }
}
