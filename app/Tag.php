<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *  @OA\Schema(
 *   schema="Tag",
 *   type="object",
 *   allOf={
 *       @OA\Schema(
 *           required={"id", "userId", "name", "description", "rbg_color_code"},
 *           @OA\Property(property="id", format="int64", type="integer"),
 *           @OA\Property(property="userId",  format="int64", type="integer"),
 *           @OA\Property(property="name", type="string"),
 *           @OA\Property(property="description",  type="string"),
 *           @OA\Property(property="rbgColorCode",  type="string"),
 *           @OA\Property(property="createdAt", type="datetime"),
 *           @OA\Property(property="updatedAt", type="datetime"),
 *          @OA\Property(property="deletedAt", type="datetime"),
 *
 *       )
 *   }
 * )
 */

class Tag extends Model
{
    protected $table = 'tags';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'name'                 ,
        'description'          ,
        'rbg_color_code'       ,
        'created_by'           ,
        'created_at'           ,
        'updated_at'           ,
        'deleted_at'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'userId'               ,
        'name'                 ,
        'description'          ,
        'rbgColorCode'         ,
        'createdBy'            ,
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
     * Get all of the projects that are assigned this tag.
     */
    public function project()
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }
}
