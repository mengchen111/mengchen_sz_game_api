<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogActivityReward extends Model
{
    public $timestamps = false;
    protected $table = 'log_activity_reward';
    protected $primaryKey = 'pid';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];
}
