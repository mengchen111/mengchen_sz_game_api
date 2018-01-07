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

    public function getRewardAttribute($value)
    {
        $rewards = ActivityReward::whereIn('pid', explode(',', $value))->get();
        return $rewards;
    }
}