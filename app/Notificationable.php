<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notificationable extends Model
{
    protected $table = 'notificationables';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                         ,
        'notification_id'            ,
        'notificationable_id'        ,
        'notificationable_type'      ,
        'created_at'                 ,
        'updated_at'                 ,
    ];


}
