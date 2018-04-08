<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityReward extends Model
{
    public $timestamps = false;
    protected $table = 'activity_reward';
    protected $primaryKey = 'pid';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function goodsTypeModel()
    {
        return $this->hasOne('App\Models\GoodsType', 'goods_id', 'goods_type');
    }

    public function logActivityReward()
    {
        return $this->hasMany('App\Models\LogActivityReward', 'pid', 'pid');
    }
}