<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Message",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "fromUserId", "fromUserId", "message"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="fromUserId",  format="int64", type="integer"),
 *           @OA\Property(property="toUserId",  format="int64", type="integer"),
 *           @OA\Property(property="message",    type="string"),
 *           @OA\Property(property="isRead",     format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'               ,
        'from_user_id'     ,
        'to_user_id'       ,
        'message'          ,
        'summary'          ,
        'is_read'          ,
        'created_at'       ,
        'updated_at'       ,
        'deleted_at'       ,

    ];

    public static $responseBody = [
        'id'               ,
        'fromUserId'       ,
        'toUserId'         ,
        'message'          ,
        'summary'          ,
        'isRead'           ,
        'createdAt'        ,
        'updatedAt'        ,
        'deletedAt'        ,
    ];


    public function fromUser()
    {
        return $this->belongsTo('App\User', 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo('App\User', 'to_user_id');
    }

    /**
     * Get all of the notifications for the project.
     */
    public function notifications()
    {
        return $this->morphToMany(Notification::class, 'notificationable', 'notificationables');
    }
}
