<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectCoCreator extends Model
{
    protected $table = 'project_co_creators';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'project_id'          ,
        'profile_id'          ,
        'created_at'          ,
        'updated_at'          ,
    ];
}
