<?php

namespace App\Http\Resources;

use App\FollowUser;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'profiles';

    public function toArray($request)
    {
        $followPivot = $this->followUsers()->withPivot('follow_by_user_id')->select('follow_by_user_id')->first();

        $check = FollowUser::where('user_id', '=', Auth::user()->id)->where('follow_by_user_id', '=', $this["id"])->count();

        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "creativeTitle"                         => $this["creative_title"],
            "biography"                             => $this["biography"],
            "location"                              => $this["location"],
            "avatarUri"                             => $this["avatar_uri"],
            "backgroundUri"                         => $this["background_uri"],
            "hudType"                               => 'profile',
            "user"                                  => new UserResource($this->user),
            'follows'                               => UserResource::collection($this->followUsers),
            'connection'                            => $this->connection,
            'profileId'                             => isset($followPivot->follow_by_user_id) ? $followPivot->follow_by_user_id : null,
            "createdAt"                             => $this["created_at"],
            "updatedAt"                             => $this["updated_at"],
            "collections"                           => $this->collections()->where('collections.user_id', Auth::user()->id)->get(),
            'socialMediaLinks'                      => SocialMediaLinkResource::collection($this->socialMediaLink),
            'updated'                               => $this['updated'],
            'checkFollow'                           => $check,
            'updatedColor'                          => $this["updated_color"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
