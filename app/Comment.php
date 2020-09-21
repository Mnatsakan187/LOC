<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Comment",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "commentText"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="commentText", type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *          @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Comment extends Model
{
    protected $table = 'comments';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'user_id'             ,
        'comment_text'        ,
        'created_at'          ,
        'updated_at'          ,


    ];

    public static $responseBody = [
        'id'                  ,
        'userId'             ,
        'commentText'        ,
        'createdAt'          ,
        'updatedAt'          ,
    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get all of the projects that are assigned this tag.
     */
    public function project()
    {
        return $this->morphedByMany(Project::class, 'commentable');
    }


    /**
     * Get the replies  for the  comment re.
     */
    public function replies()
    {
        return $this->hasMany('App\Reply')->orderBy('created_at', 'desc');
    }

}
