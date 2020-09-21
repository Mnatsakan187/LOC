<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *  @OA\Schema(
 *   schema="Profile",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "creativeTitle"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId", format="int64", type="integer"),
 *           @OA\Property(property="creativeTitle", type="string"),
 *           @OA\Property(property="biography", type="string"),
 *           @OA\Property(property="location", type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *       )
 *   }
 * )
 */

class Profile extends Model
{
    use SoftDeletes;

    protected $table = 'profiles';
    protected $guarded = ['id'];
    protected $softDelete = true;
    protected $dates = ['deleted_at'];


    protected $fillable = [
        "id",
        "user_id",
        "created_at",
        "updated_at",
        "creative_title",
        "biography",
        "location",
        'update',
        "avatar_uri",
        "background_uri",
        "updated_color",
        "updated",
        "pin_to_top"
    ];

    public static $responseBody = [
        "id",
        "userId",
        "createdAt",
        "updatedAt",
        "creativeTitle",
        "biography",
        "location",
        'update',
        "avatarUri",
        "backgroundUri",
        "updatedColor",
        "updated",
        "pinToTop"
    ];


    protected static function boot()
    {
        parent::boot();

        static::updated(function ($model) {
            Profile::where('id', $model->id)->update(['updated_color' => 1]);
            $collections = $model->collections()->get();
            foreach ($collections as $collection){
                Collection::where('id', $collection->id)->update(['updated' => 1, 'updated_at' => Carbon::now()->toDateTimeString()]);
            }
        });

    }

    /**
     * Get the  likes .
     */
    public function socialMediaLink()
    {
        return $this->hasMany('App\SocialMediaLink');
    }


    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id','id');
    }

    /**
     * Get all of the medium for the project.
     */
    public function medium()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }

    /**
     * The roles that belong to the user.
     */
    public function followUsers()
    {
        return $this->belongsToMany('App\User', 'follow_users', 'follow_by_user_id');
    }

    /**
     * Get all of the collections for the project.
     */
    public function collections()
    {
        return $this->morphToMany(Collection::class, 'collectionable');
    }

    /**
     * Get all of the teams for the profile.
     */
    public function teams()
    {
        return $this->morphToMany(Profile::class, 'teamable', 'teamables', 'teamable_id', 'user_id');
    }


    /**
     * The roles that belong to the user.
     */
    public function connection()
    {
        return $this->belongsToMany('App\User', 'connection', 'profile_id')->withPivot('accept');
    }

    /**
     * Get all of the likes for the project.
     */
    public function likes()
    {
        return $this->morphToMany(Like::class, 'likeable')->withPivot('likeable_id');
    }

    /**
     * Get the poll record associated with the profile.
     */
    public function poll()
    {
        return $this->hasOne('App\Poll');
    }

    /**
     * Get the post record associated with the profile.
     */
    public function post()
    {
        return $this->hasOne('App\Post');
    }

    /**
     * Get the project record associated with the profile.
     */
    public function project()
    {
        return $this->hasOne('App\Project');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function hides()
    {
        return $this->morphMany('App\Hide', 'hideable');
    }






}
