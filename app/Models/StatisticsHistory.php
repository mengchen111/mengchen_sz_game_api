<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticsHistory extends Model
{
    public $timestamps = false;
    protected $table = 'statistics_history';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];
}
