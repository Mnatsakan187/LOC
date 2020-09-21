<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Notification",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "summary"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="createdBy",  format="int64", type="integer"),
 *           @OA\Property(property="isRead",  format="int64", type="integer"),
 *           @OA\Property(property="summary", type="string"),
 *           @OA\Property(property="type",    type="string"),
 *           @OA\Property(property="readDate",  type="datetime"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Notification extends Model
{
    protected $table = 'notifications';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'summary'              ,
        'created_by'           ,
        'is_read'              ,
        'read_date'            ,
        'type'                 ,
        'profile_id'           ,
        'name'                 ,
        'created_at'           ,
        'updated_at'           ,
        'deleted_at'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'summary'              ,
        'createdBy'            ,
        'isRead'               ,
        'readDate'             ,
        'type'                 ,
        'profileId'            ,
        'name'                 ,
        'createdAt'            ,
        'updatedAt'            ,
        'deletedAt'            ,
    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    /**
     * Get the user that owns the project.
     */
    public function profile()
    {
        return $this->belongsTo('App\Profile', 'profile_id');
    }


    /**
     * Get all of the projects that are assigned this notification.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'notificationable');
    }

    /**
     * Get all of the events that are assigned this notification.
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'notificationable');
    }

    /**
     * Get all of the posts that are assigned this notification.
     */
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'notificationable');
    }


    /**
     * Get all of the posts that are assigned this notification.
     */
    public function polls()
    {
        return $this->morphedByMany(Poll::class, 'notificationable');
    }

}
