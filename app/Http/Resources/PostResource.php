<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;


class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'posts';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "profileId"                             => $this["profile_id"],
            "groupId"                               => $this["group_id"],
            "projectId"                             => $this["project_id"],
            "summary"                               => $this["summary"],
            "description"                           => $this["description"],
            "imageUri"                              => $this["image_uri"],
            "createdAt"                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "shareCount"                            => $this['share_count'],
            "createdBy"                             => $this['created_by'],
            'hudType'                               => 'post',
            "profile"                               => new ProfileResource($this->profile()->first()),
            "images"                                => MediaResource::collection($this->media()->where('media_type', 0)->orderBy('id')->get()),
            "links"                                 => MediaResource::collection($this->media()->where('media_type', 5)->orderBy('id', 'asc')->get()),
            "likes"                                 => new LikeResource($this->likes()->where('likes.user_id', Auth::user()->id)->first()),
            "likeCount"                             =>  $this->likes()->count(),
            "comments"                              => CommentResource::collection($this->comments()->orderBy('comments.id', 'desc')->get()),
            "commentCount"                          => $this->comments()->count(),
            'group'                                 => [],
            'project'                               => [],
//            'postProfiles'                          => ProfileResource::collection($this->postProfile()->get()),
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
