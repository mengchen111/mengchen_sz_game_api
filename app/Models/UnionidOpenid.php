<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnionidOpenid extends Model
{
    public $timestamps = false;
    protected $table = 'unionid_openid';
    protected $primaryKey = 'unionid';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [
        //
    ];

    protected function player()
    {
        return $this->hasOne('App\Models\Players', 'unionid', 'unionid');
    }
}