<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="FollowUser",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "followByUserId", "userId", "followDate" },
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="followByUserId",  format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="followDate",  type="datetime"),
 *           @OA\Property(property="createdAt",  type="datetime"),
 *           @OA\Property(property="updatedAt",  type="datetime"),
 *
 *       )
 *   }
 * )
 */

class FollowUser extends Model
{
    protected $table = 'follow_users';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'follow_by_user_id'    ,
        'user_id'              ,
        'follow_date'          ,
        'created_at'           ,
        'updated_at'           ,
    ];

    public static $responseBody = [
        'id'                  ,
        'followByUserId'      ,
        'userId'              ,
        'followDate'          ,
        'createdAt'           ,
        'updatedAt'           ,
    ];


}
