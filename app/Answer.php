<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Answer",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "pollId", "answer"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="pollId",  format="int64", type="integer"),
 *           @OA\Property(property="answer", type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *          @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Answer extends Model
{
    protected $table = 'answers';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'poll_id'             ,
        'answer'              ,
        'created_at'          ,
        'updated_at'          ,


    ];

    public static $responseBody = [
        'id'                 ,
        'pollId'             ,
        'answer'             ,
        'createdAt'          ,
        'updatedAt'          ,
    ];

    /**
     * Get the poll that owns the answer.
     */
    public function poll()
    {
        return $this->belongsTo('App\Poll');
    }


}
