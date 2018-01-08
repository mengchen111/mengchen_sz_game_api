<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsType extends Model
{
    public $timestamps = false;
    protected $table = 'goods_type';
    protected $primaryKey = 'goods_id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];
}