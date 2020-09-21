<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mediable extends Model
{
    protected $table = 'mediables';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'media_id'            ,
        'mediable_id'         ,
        'mediable_type'       ,
        'created_at'          ,
        'updated_at'          ,
    ];
}
