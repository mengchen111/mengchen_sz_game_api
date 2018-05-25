<?php

namespace App\Models\V1;

use App\Models\Players;
use App\Traits\RoomPlayers;
use Illuminate\Database\Eloquent\Model;

class RoomsHistory extends Model
{
    use RoomPlayers;

    protected $table = 'rooms_history';
    public $timestamps = false;

    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }

    public function creator()
    {
        return $this->belongsTo(Players::class, 'id', 'creator');
    }

    public function roomPlayers()
    {
        return $this->hasMany(RoomsHistoryPlayer::class, 'ruid', 'ruid');
    }

    public function getRecords()
    {
        //todo 加上kind约束（预留）
        $records = RecordRelative::with('infos')
            ->where('ruid', $this->ruid)
            ->get()
            ->toArray();
        return $records;
    }

}
