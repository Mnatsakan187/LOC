<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Likeable extends Model
{
    protected $table = 'likeables';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'like_id'              ,
        'likeable_id'         ,
        'likeable_type'       ,
        'created_at'          ,
        'updated_at'          ,
    ];
}
