<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Post",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "summary"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="summary",  type="string"),
 *           @OA\Property(property="description",  type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Post extends Model
{
    protected $table = 'posts';
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
        'group_id'             ,
        'project_id'           ,
        'summary'              ,
        'description'          ,
        'image_uri'            ,
        'share_count'          ,
        'created_by'           ,
        'created_at'           ,
        'updated_at'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'profileId'            ,
        'groupId'              ,
        'projectId'            ,
        'summary'              ,
        'description'          ,
        'image_uri'            ,
        'shareCount'           ,
        'createdBy'            ,
        'createdAt'            ,
        'updatedAt'            ,
    ];

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
     * Get all of the medium for the post.
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
     * Get all of the profile for the post.
     */
    public function profile()
    {
        return $this->belongsTo(Profile::class, 'profile_id', 'id');
    }

    /**
     * Get all of the project for the post.
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }


    /**
     * Get all of the group for the post.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }


    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }


    /**
     * Get all of the profiles.
     */
    public function postProfile()
    {
        return $this->belongsToMany('App\Profile', 'post_profiles', 'post_id', 'profile_id');
    }



}
