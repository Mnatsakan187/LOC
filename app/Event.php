<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Event",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name", },
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="name",  type="string"),
 *           @OA\Property(property="date",  type="datetime"),
 *           @OA\Property(property="durationInHours",  type="string"),
 *           @OA\Property(property="venue",  type="string"),
 *           @OA\Property(property="streetAdress", type="string"),
 *           @OA\Property(property="number", type="string"),
 *           @OA\Property(property="postalCode", type="string"),
 *           @OA\Property(property="city", type="string"),
 *           @OA\Property(property="town", type="string"),
 *           @OA\Property(property="country", type="string"),
 *           @OA\Property(property="latitude", type="string"),
 *           @OA\Property(property="longitud", type="string"),
 *           @OA\Property(property="isPublished", format="int64", type="integer"),
 *           @OA\Property(property="cost",  type="decimal"),
 *           @OA\Property(property="createdAt",  type="datetime"),
 *           @OA\Property(property="updatedAt",  type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Event extends Model
{
    protected $table = 'events';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'profile_id'           ,
        'category_id'          ,
        'name'                 ,
        'date'                 ,
        'duration_in_hours'    ,
        'venue'                ,
        'street_adress'        ,
        'number'               ,
        'postal_code'          ,
        'city'                 ,
        'town'                 ,
        'country'              ,
        'latitude'             ,
        'longitud'             ,
        'is_published'         ,
        'cost'                 ,
        'poster_uri'           ,
        'background_uri'       ,
        'start_date'           ,
        'end_date'             ,
        'description'          ,
        'created_at'           ,
        'updated_at'           ,
        'update'               ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'profileId'           ,
        'categoryId'          ,
        'name'                 ,
        'date'                 ,
        'durationInHours'      ,
        'venue'                ,
        'streetAdress'         ,
        'number'               ,
        'postalCode'           ,
        'city'                 ,
        'town'                 ,
        'country'              ,
        'latitude'             ,
        'longitud'             ,
        'isPublished'          ,
        'cost'                 ,
        'posterUri'            ,
        'backgroundUri'        ,
        'startDate'            ,
        'endDate'              ,
        'description'          ,
        'createdAt'            ,
        'updatedAt'            ,
        'update'               ,
    ];


    protected static function boot()
    {
        parent::boot();

        static::updated(function ($model) {
            $collections = $model->collections()->get();
            foreach ($collections as $collection){
                Collection::where('id', $collection->id)->update(['updated' => 1, 'updated_at' => Carbon::now()->toDateTimeString()]);
            }
        });

    }

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get all of the tags for the post.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get all of the likes for the event.
     */
    public function likes()
    {
        return $this->morphToMany(Like::class, 'likeable');
    }


    /**
     * Get all of the medium for the event.
     */
    public function media()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }


    /**
     * Get all of the comments for the post.
     */
    public function comments()
    {
        return $this->morphToMany(Comment::class, 'commentable', 'commentable');
    }

    /**
     * Get the category  record associated with the event.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    /**
     * Get all of the collections for the event.
     */
    public function collections()
    {
        return $this->morphToMany(Collection::class, 'collectionable');
    }


    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function hides()
    {
        return $this->morphMany('App\Hide', 'hideable');
    }
}
