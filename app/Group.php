<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Group",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name", "isVisible"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId", format="int64", type="integer"),
 *           @OA\Property(property="name", type="string"),
 *           @OA\Property(property="description", type="string"),
 *           @OA\Property(property="createdBy", format="int64", type="integer"),
 *           @OA\Property(property="updatedBy", format="int64", type="integer"),
 *           @OA\Property(property="isVisible", format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *       )
 *   }
 * )
 */

class Group extends Model
{
    protected $table = 'groups';
    protected $guarded = ['id'];

    protected $fillable = [
        "id"              ,
        "user_id"         ,
        "name"            ,
        "description"     ,
        "created_by"      ,
        "updated_by"      ,
        "is_visible"      ,
        "created_at"      ,
        "updated_at"      ,
        "deleted_at"      ,
    ];

    public static $responseBody = [
        "id"             ,
        "userId"         ,
        "name"           ,
        "description"    ,
        "createdBy"      ,
        "updatedBy"      ,
        "isVisible"      ,
        "createdAt"      ,
        "updatedAt"      ,
        "deletedAt"      ,
    ];

    /**
     * Get the plan that owns the node.
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id','id');
    }

    /**
     * Get the users that owns the group.
     */
    public function users()
    {
        return $this->belongsToMany('App\User', 'group_user');
    }

    /**
     * Get the posts that owns the group.
     */
    public function posts()
    {
        return $this->hasMany('App\Post');
    }
}
