<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialMediaLink extends Model
{
    protected $table = 'social_media_links';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'name'                 ,
        'social_media_link'    ,

    ];


}
