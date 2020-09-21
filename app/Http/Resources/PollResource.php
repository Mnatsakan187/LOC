<?php

namespace App\Http\Resources;

use App\UserPollAnswer;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class PollResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'polls';

    public function toArray($request)
    {
        $authUser = Auth::user();

        /** @var UserPollAnswer|null $authUserPoolAnswer */
        $authUserPoolAnswer = $this->resource->getUserPoolAnswerForUser($authUser);

        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "profileId"                             => $this["profile_id"],
            "projectId"                             => $this["project_id"],
            "name"                                  => $this["name"],
            "question"                              => $this["question"],
            "answerType"                            => $this["answer_type"],
            "answer"                                => $this["answer"],
            "createdAt"                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"],
            "answers"                               => AnswerResource::collection($this->answers),
            "userPollAnswer"                        => new UserPollAnswerResource($authUserPoolAnswer ? $authUserPoolAnswer->toArray(): null),
            "profile"                             => $this->profile()->count() ? new ProfileResource($this->profile()->first()) : '',
            'hudType'                               => 'poll',
            "likes"                                 => new LikeResource($this->likes()->where('likes.user_id', Auth::user()->id)->first()),
            "likeCount"                             =>  $this->likes()->count(),
            "comments"                              => CommentResource::collection($this->comments()->orderBy('comments.id', 'desc')->get()),
            "commentCount"                          => $this->comments()->count(),
            "fillAnswers"                           => [],
            "project"                               => [],
            "disableRespond"                        => $this->resource->doNeedToDisableRespond($authUser)

        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
