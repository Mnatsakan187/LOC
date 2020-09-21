<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Category extends Model
{
    protected $table = 'categories';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'name'                 ,
        'created_at'           ,
        'updated_at'           ,

    ];

    public static $responseBody = [
        'id'                   ,
        'name'                 ,
        'createdAt'            ,
        'updatedAt'            ,
    ];


}
