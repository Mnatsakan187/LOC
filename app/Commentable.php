<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commentable extends Model
{
    protected $table = 'commentable';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'comment_id'          ,
        'commentable_id'      ,
        'commentable_type'    ,
        'created_at'          ,
        'updated_at'          ,
    ];
}
