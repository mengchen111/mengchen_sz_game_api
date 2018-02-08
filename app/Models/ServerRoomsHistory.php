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

    public function getRecordInfoAttribute()
    {
        //使用like查询为不是relationship是因为未知的原因where查询bigint有时候查询不到
        return RecordInfosNew::where('ruid', 'like', $this->attributes['ruid'])->first();
    }

//    public function getRuidAttribute($value)
//    {
//        return (string) $value; //转成字符串，不然前端读取有问题
//    }

    public function creator()
    {
        return $this->hasOne('App\Models\Players', 'id', 'creator_uid');
    }

    public function player1()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_1');
    }

    public function player2()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_2');
    }

    public function player3()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_3');
    }

    public function player4()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_4');
    }

    public function getKindAttribute()
    {
        return 4;
    }
}
