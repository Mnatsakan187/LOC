<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Collection",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId", format="int64", type="integer"),
 *           @OA\Property(property="name", type="string"),
 *           @OA\Property(property="updatedBy", format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *       )
 *   }
 * )
 */

class Collection extends Model
{
    protected $table = 'collections';
    protected $guarded = ['id'];

    protected $fillable = [
        "id"              ,
        "user_id"         ,
        "name"            ,
        'background_uri'  ,
        'profile_id'      ,
        "updated_by"      ,
        'update'          ,
        "created_at"      ,
        "updated_at"      ,
        "deleted_at"      ,
    ];

    public static $responseBody = [
        "id"             ,
        "userId"         ,
        "name"           ,
        'backgroundUri'  ,
        'profileId'      ,
        "updatedBy"      ,
        'update'         ,
        "createdAt"      ,
        "updatedAt"      ,
        "deletedAt"      ,
    ];

//    protected static function boot()
//    {
//        parent::boot();
//
//        static::updated(function ($model) {
//            Collection::where('id', $model->id)->update(['updated' => 1]);
//        });
//
//    }

    /**
     * Get the plan that owns the node.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id','id');
    }

    /**
     * Get all of the projects that are assigned this collection.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'collectionable');
    }


    /**
     * Get all of the events that are assigned this collection.
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'collectionable');
    }

    /**
     * Get all of the creators that are assigned this collection.
     */
    public function creators()
    {
        return $this->morphedByMany(User::class, 'collectionable', 'collectionables', 'collectionable_id', 'collection_id');
    }


    /**
     * Get all of the projects that are assigned this collection.
     */
    public function profiles()
    {
        return $this->morphedByMany(Profile::class, 'collectionable');
    }




}
