<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostProfile extends Model
{
    use SoftDeletes;

    protected $table = 'post_profiles';
    protected $guarded = ['id'];
    protected $softDelete = true;
    protected $dates = ['deleted_at'];


    protected $fillable = [
        "id",
        "post_id",
        "profile_id",
        "created_at",
        "updated_at",
    ];

    public static $responseBody = [
        "id",
        "post_id",
        "profile_id",
        "created_at",
        "updated_at",
    ];
}
