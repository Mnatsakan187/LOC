<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Like",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "likedDate"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="likedDate",  type="datetime"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Like extends Model
{
    protected $table = 'likes';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'liked_date'            ,
        'created_at'           ,
        'updated_at'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'likedDate'                 ,
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
     * Get all of the projects that are assigned this like.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'likeable');
    }

    /**
     * Get all of the events that are assigned this like.
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'likeable');
    }

    /**
     * Get all of the posts that are assigned this like.
     */
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'likeable');
    }
}
