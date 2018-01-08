<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    public $timestamps = false;
    protected $table = 'tasks';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public $appends = [
        'reward_good', 'reward_count',
    ];

    public function typeModel()
    {
        return $this->hasOne('App\Models\TaskType', 'id', 'type');
    }

    public function getRewardGoodAttribute()
    {
        $goodId = explode('_', $this->attributes['reward'])[0];
        $good = GoodsType::findOrFail($goodId);
        return $good->goods_name;
    }

    public function getRewardCountAttribute()
    {
        return explode('_', $this->attributes['reward'])[1];
    }
}