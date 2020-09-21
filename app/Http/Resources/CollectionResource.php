<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    public $preserveKeys    = true;
    protected $primaryKey   = 'id';
    protected $table        = 'collections';

    public function toArray($request)
    {

        $collections =  collect();
        foreach ($this->projects as $project) {
            $collections->push(new ProjectResource($project));
        }

        foreach ($this->events as $event) {
            $collections->push(new EventResource($event));
        }

        foreach ($this->creators as $creator) {
            $collections->push(new UserResource($creator));
        }


        foreach ($this->profiles as $profile) {
            $collections->push(new ProfileResource($profile));
        }



        $collections = $collections->sortByDesc('created_at');

        $sortedCollections = collect();
        foreach ($collections as $item){
            $sortedCollections->push($item);
        }





        return [
            "id"                                    => $this["id"],
            "userId"                                => $this["user_id"],
            "profileId"                             => $this["profile_id"],
            "name"                                  => $this["name"],
            "backgroundUri"                         => $this["background_uri"],
            "updatedBy"                             => $this["updated_by"],
            "createdAt"                             => $this["created_at"],
            'collectionables'                       => $sortedCollections,
            "user"                                  => new UserResource($this->user),
            'hudType'                               => 'collection',
            "updatedAt"                             => $this["updated_at"],
            "deletedAt"                             => $this["deleted_at"]
        ];
    }

    public function with($request) {

        return [
            'self' => url()->current()
        ];
    }
}
