<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogRedbag extends Model
{
    public $timestamps = false;
    protected $table = 'log_redbag';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function activityReward()
    {
        return $this->hasOne('App\Models\ActivityReward', 'pid', 'reward_id');
    }

    public function player()
    {
        return $this->hasOne('App\Models\Players', 'id', 'user_id');
    }
}
