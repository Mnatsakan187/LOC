<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanFeature extends Model
{
    protected $table = 'plan_features';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'plan_id'             ,
        'name'                ,
        'check'               ,
        'info'                ,
        'included'            ,
        'created_at'          ,
        'updated_at'          ,
        'deleted_at'          ,
    ];

    public static $responseBody = [
        'id'                  ,
        'planId'              ,
        'name'                ,
        'check'               ,
        'info'                ,
        'included'            ,
        'createdAt'           ,
        'updatedAt'           ,
        'deletedAt'           ,
    ];
}
