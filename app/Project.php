<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Project",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name", "type"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="name", type="string"),
 *           @OA\Property(property="isPublished",  format="int64", type="integer"),
 *           @OA\Property(property="description",  type="string"),
 *           @OA\Property(property="type",  format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *          @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

/**
 * @property-read User $user
 *
 * Class Project
 * @package App
 */
class Project extends Model
{
    protected $table = 'projects';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                        ,
        'user_id'                   ,
        'profile_id'                ,
        'name'                      ,
        'is_published'              ,
        'description'               ,
        'type'                      ,
        'background_uri'            ,
        'original_background_uri'   ,
        'avatar_uri'                ,
        'share_count'               ,
        'created_at'                ,
        'updated_at'                ,
        'updated'                   ,
        'pin_to_top'                ,

    ];

    public static $responseBody = [
        'id'                       ,
        'userId'                   ,
        'profileId'                ,
        'name'                     ,
        'isPublished'              ,
        'description'              ,
        'type'                     ,
        'backgroundUri'            ,
        'originalBackgroundUri'    ,
        'avatarUri'                ,
        'shareCount'               ,
        'createdAt'                ,
        'updatedAt'                ,
        'updated'                  ,
        'pinToTop'                 ,
    ];


    protected static function boot()
    {
        parent::boot();
        static::updated(function ($model) {
            Project::where('id', $model->id)->update(['updated' => 1, 'updated_color' => 1]);
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
     * Get all of the tags for the project.
     */
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * Get all of the collections for the project.
     */
    public function collections()
    {
        return $this->morphToMany(Collection::class, 'collectionable');
    }

    /**
     * Get all of the likes for the project.
     */
    public function likes()
    {
        return $this->morphToMany(Like::class, 'likeable')->withPivot('likeable_id');
    }

    /**
     * Get all of the media for the project.
     */
    public function media()
    {
        return $this->morphToMany(Media::class, 'mediable');
    }

    /**
     * Get all of the comments for the project.
     */
    public function comments()
    {
        return $this->morphToMany(Comment::class, 'commentable', 'commentable')->orderBy('created_at', 'desc');
    }


    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }


    /**
     * Get all of the teams for the project.
     */
    public function teams()
    {
        return $this->morphToMany(Profile::class, 'teamable', 'teamables', 'teamable_id', 'user_id');
    }

    /**
     * Get all of the collections for the project.
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'project_id', 'id');
    }


    /**
     * Get all of the collections for the project.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }



    /**
     * Get all of the collections for the project.
     */
    public function polls()
    {
        return $this->hasMany(Poll::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function hides()
    {
        return $this->morphMany('App\Hide', 'hideable');
    }
}
