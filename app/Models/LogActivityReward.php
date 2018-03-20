<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivityReward extends Model
{
    public $timestamps = false;
    protected $table = 'log_activity_reward';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function activityReward()
    {
        return $this->hasOne('App\Models\ActivityReward', 'pid', 'pid');
    }

    public function player()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid');
    }
}
