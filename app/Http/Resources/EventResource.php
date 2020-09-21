<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'events';

    public function toArray($request)
    {


        return [
            "id"                        => $this["id"],
            "userId"                    => $this["user_id"],
            "profileId"                 => $this["profile_id"],
            "categoryId"                => $this["category_id"],
            "name"                      => $this["name"],
            "date"                      => $this["date"],
            "durationInHours"           => $this["duration_in_hours"],
            "venue"                     => $this["venue"],
            "streetAdress"              => $this["street_adress"],
            "number"                    => $this["number"],
            "postalCode"                => $this["postal_code"],
            "city"                      => $this["city"],
            "town"                      => $this["town"],
            "country"                   => $this["country"],
            "latitude"                  => (int)$this["latitude"],
            "longitud"                  => (int)$this["longitud"],
            "isPublished"               => $this["is_published"],
            "cost"                      => $this["cost"],
            "backgroundUri"             => $this["background_uri"],
            "posterUri"                 => $this["poster_uri"],
            "description"               => $this["description"],
            "startDate"                 => $this["start_date"],
            "endDate"                   => $this["end_date"],
            "category"                  => new CategoryResource($this->category),
            "likes"                     => new LikeResource($this->likes()->where('likes.user_id', Auth::user()->id)->first()),
            "likeCount"                 =>  $this->likes()->count(),
            "comments"                  => CommentResource::collection($this->comments()->orderBy('comments.id', 'desc')->get()),
            "collections"               => $this->collections()->where('collections.user_id', Auth::user()->id)->get(),
            "commentCount"              => $this->comments()->count(),
            "user"                      => new UserResource($this->user),
            'hudType'                   => 'event',
            "createdAt"                 => $this["created_at"],
            "updatedAt"                 => $this["updated_at"],
            'updated'                   => $this['updated'],
            'updatedColor'              => $this["updated_color"],
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
