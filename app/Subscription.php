<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table = 'subscriptions';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id'                   ,
        'user_id'              ,
        'plan_id'              ,
        'group_id'             ,
        'summary'              ,
        'description'          ,
        'image_uri'            ,
        'subscription_id'      ,
        'created_at'           ,
        'updated_at'           ,

    ];


    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo('App\Post');
    }


}
