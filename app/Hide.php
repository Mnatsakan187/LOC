<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read Model $hideable
 *
 * Class Hide
 * @package App
 */
class Hide extends Model
{
    protected $fillable = [
        'user_id',
        'hideable_id',
        'hideable_type'
    ];

    public function hideable()
    {
        return $this->morphTo();
    }
}
