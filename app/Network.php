<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{
    protected $table = 'networks';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                  ,
        'user_id'             ,
        'network_user_id'     ,
        'created_at'          ,
        'updated_at'          ,
    ];



}
