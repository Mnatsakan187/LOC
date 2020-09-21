<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Teamable extends Model
{
    protected $table = 'teamables';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'user_id'             ,
        'teamable_id'         ,
        'teamable_type'       ,
        'created_at'          ,
        'updated_at'          ,
    ];
}
