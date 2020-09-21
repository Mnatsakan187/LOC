<?php

namespace App\Http\Resources;

use App\Like;
use App\Media;
use App\Poll;
use App\Tag;
use App\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'projects';

    public function toArray($request)
    {
        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "profileId"                             => $this["profile_id"],
            "name"                                  => $this["name"],
            "isPublished"                           => $this["is_published"],
            "description"                           => $this["description"],
            "type"                                  => $this["type"],
            "backgroundUri"                         => $this["background_uri"],
            "originalBackgroundUri"                 => $this["original_background_uri"],
            "createdAt"                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "shareCount"                            => $this['share_count'],
            "avatarUri"                             => $this['avatar_uri'],
            "tags"                                  => TagResource::collection($this->tags),
            "user"                                  => new UserResource($this->user),
            "images"                                => MediaResource::collection($this->media()->where('media_type', 0)->get()),
            "texts"                                 => MediaResource::collection($this->media()->where('media_type', 4)->get()),
            "links"                                 => MediaResource::collection($this->media()->where('media_type', 5)->get()),
            "media"                                 => MediaResource::collection($this->media()->orderBy('id', 'desc')->get()),
            "comments"                              => CommentResource::collection($this->comments()->orderBy('comments.id', 'desc')->get()),
            "collections"                           => $this->collections()->where('collections.user_id', Auth::user()->id)->get(),
            "likes"                                 => new LikeResource($this->likes()->where('likes.user_id', Auth::user()->id)->first()),
            "likesAll"                              => LikeResource::collection($this->likes),
            "likeCount"                             => $this->likes()->count(),
            "commentCount"                          => $this->comments()->count(),
            "posts"                                 => PostResource::collection($this->posts()->orderBy('created_at', 'desc')->get()),
            "polls"                                 => PollResource::collection($this->polls()->orderBy('created_at', 'desc')->get()),
            "profile"                               => $this->profile()->count() ? new ProfileResource($this->profile()->first()) : '',
            'hudType'                               => 'project',
            'updated'                               => $this['updated'],
            'updatedColor'                          => $this["updated_color"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
