<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Media",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name", "uri", "mediaType"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="displayName",  type="string"),
 *           @OA\Property(property="fieldName",  type="string"),
 *           @OA\Property(property="uri",  type="string"),
 *           @OA\Property(property="createdBy",  format="int64", type="integer"),
 *           @OA\Property(property="updatedBy",  format="int64", type="integer"),
 *           @OA\Property(property="mediaType",  format="int64", type="integer"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *           @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Media extends Model
{
    protected $table = 'media';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'display_name'         ,
        'field_name'           ,
        'uri'                  ,
        'created_by'           ,
        'updated_by'           ,
        'media_type'           ,
        'created_by_image'     ,
        'created_at'           ,
        'updated_at	'          ,
        'deleted_at	'          ,
    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'displayName'          ,
        'fieldName'            ,
        'uri'                  ,
        'createdBy'            ,
        'updatedBy'            ,
        'mediaType'            ,
        'createdByImage'       ,
        'createdAt'            ,
        'updatedAt'            ,
        'deletedAt'            ,
    ];

    /**
     * Get the user that owns the project.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get all of the projects that are assigned this media.
     */
    public function projects()
    {
        return $this->morphedByMany(Project::class, 'mediables');
    }

    /**
     * Get all of the events that are assigned this media.
     */
    public function events()
    {
        return $this->morphedByMany(Event::class, 'mediables');
    }

    /**
     * Get all of the posts that are assigned this media.
     */
    public function posts()
    {
        return $this->morphedByMany(Post::class, 'mediables');
    }

    /**
     * Get all of the profiles that are assigned this media.
     */
    public function profiles()
    {
        return $this->morphedByMany(Profile::class, 'mediables');
    }

    /**
     * Get all of the profiles that are assigned this media.
     */
    public function users()
    {
        return $this->morphedByMany(User::class, 'mediables');
    }
}
