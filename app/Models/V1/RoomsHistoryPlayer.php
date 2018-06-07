<?php

namespace App\Models\V1;

use App\Models\Players;
use Illuminate\Database\Eloquent\Model;

class RoomsHistoryPlayer extends Model
{
    public $timestamps = false;
    protected $table = 'rooms_history_player';
    protected $primaryKey = 'ruid';

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }
    public function player()
    {
        return $this->belongsTo(Players::class, 'uid');
    }
}
