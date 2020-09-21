<?php

namespace App\Http\Resources;

use App\Answer;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPollAnswerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'user_poll_answers';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "pollId"                                => $this["poll_id"],
            "selectAnswerId"                        => $this["select_answer_id"],
            "openAnswer"                            => $this["open_answer"],
            "answerType"                            => $this["answer_type"],
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
