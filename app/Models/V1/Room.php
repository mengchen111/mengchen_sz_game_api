<?php

namespace App\Models\V1;

use App\Traits\RoomPlayers;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Room extends Model
{
    use RoomPlayers;

    public $timestamps = false;
    protected $appends = [
        'create_date',
    ];

    public function roomPlayers()
    {
        return $this->hasMany(RoomsPlayer::class, 'ruid', 'ruid');
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCreateDateAttribute()
    {
        return Carbon::parse($this->attributes['ctime'])->toDateString();
    }

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }
}
