<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGoods extends Model
{
    public $timestamps = false;
    protected $table = 'user_goods';
    protected $primaryKey = 'user_id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function goodsTypeModel()
    {
        return $this->hasOne('App\Models\GoodsType', 'goods_id', 'goods_id');
    }

    public function player()
    {
        return $this->hasOne('App\Models\Players', 'id', 'user_id');
    }
}