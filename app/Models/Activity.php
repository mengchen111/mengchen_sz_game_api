<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public $timestamps = false;
    protected $table = 'activity';
    protected $primaryKey = 'aid';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [
        'reward_model',
        'tasks_model',
    ];

    public function getRewardModelAttribute()
    {
        $rewards = ActivityReward::with('goodsTypeModel')->whereIn('pid', explode(',', $this->attributes['reward']))->get();
        return $rewards;
    }

    public function getTasksModelAttribute()
    {
        $rewards = Tasks::whereIn('id', explode(',', $this->attributes['task']))->get();
        return $rewards;
    }
}