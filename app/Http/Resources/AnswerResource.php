<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'answers';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "pollId"                                => $this["poll_id"],
            "answer"                                => $this["answer"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
