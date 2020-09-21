<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Plan extends Model
{
    protected $table = 'plans';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'name'                ,
        'price'               ,
        'created_at'          ,
        'updated_at'          ,
        'deleted_at'          ,
    ];

    public static $responseBody = [
        'id'                  ,
        'name'                ,
        'price'               ,
        'createdAt'           ,
        'updatedAt'           ,
        'deletedAt'           ,
    ];


    /**
     * Get the comments for the blog post.
     */
    public function planFeature()
    {
        return $this->hasMany('App\PlanFeature');
    }


}
