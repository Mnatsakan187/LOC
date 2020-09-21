<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="UserPollAnswer",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "pollId", "selectAnswerId", "openAnswer", "answerType"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="pollId",  format="int64", type="integer"),
 *           @OA\Property(property="selectAnswerId",  format="int64", type="integer"),
 *           @OA\Property(property="openAnswer",  type="string"),
 *           @OA\Property(property="answerType",  format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

/**
 * @property-read User $user
 *
 * Class UserPollAnswer
 * @package App
 */
class UserPollAnswer extends Model
{
    protected $table = 'user_poll_answers';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'user_id'             ,
        'poll_id'             ,
        'select_answer_id'    ,
        'open_answer'         ,
        'answer_type'         ,
        'created_at'          ,
        'updated_at'          ,
        'deleted_at'          ,


    ];

    public static $responseBody = [
        'id'                  ,
        'userId'             ,
        'pollId'             ,
        'selectAnswerId'    ,
        'openAnswer'         ,
        'answerType'         ,
        'createdAt'          ,
        'updatedAt'          ,
        'deletedAt'          ,

    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }
}
