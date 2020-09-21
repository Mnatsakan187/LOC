<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Reply",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "commentId", "commentText"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="commentId",  format="int64", type="integer"),
 *           @OA\Property(property="replyText", type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *          @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Reply extends Model
{
    protected $table = 'replies';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'user_id'             ,
        'comment_id'          ,
        'reply_text'        ,
        'created_at'          ,
        'updated_at'          ,


    ];

    public static $responseBody = [
        'id'                 ,
        'userId'             ,
        'commentId'          ,
        'replyText'          ,
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


}
