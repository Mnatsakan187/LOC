<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Connect",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "profileId"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="profileId",  format="int64", type="integer"),
 *           @OA\Property(property="profileId",  format="int64", type="accept"),
 *           @OA\Property(property="createdAt",  type="datetime"),
 *           @OA\Property(property="updatedAt",  type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Connect extends Model
{
    protected $table = 'connection';
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
        'accept'               ,
        'date'               ,
        'created_at'           ,
        'updated_at'           ,
    ];

    public static $responseBody = [
        'id'                  ,
        'userId'              ,
        'profileId'           ,
        'accept'              ,
        'date'                ,
        'createdAt'           ,
        'updatedAt'           ,
    ];


}
