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
}