<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerRoomsHistory extends Model
{
    public $timestamps = false;
    protected $table = 'server_rooms_history_4';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];

    public function getOptionsJstrAttribute($value)
    {
        return json_decode($value, true);
    }

    public function recordInfo()
    {
        return $this->hasOne('App\Models\RecordInfosNew', 'ruid', 'ruid');
    }

    public function getRuidAttribute($value)
    {
        return (string) $value; //转成字符串，不然前端读取有问题
    }
}
