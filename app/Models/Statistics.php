<?php
/**
 * type 1 当前在线人数
 * type 2 当前最高人数
 * type 3 游戏中的人数
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statistics extends Model
{
    public $timestamps = false;
    protected $table = 'statistics';
    protected $primaryKey = 'type';
        //type 1 当前在线人数
        //type 2 当前最高人数
        //type 3 游戏中的人数

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];
}
