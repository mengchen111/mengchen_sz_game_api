<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Players extends Model
{
    public $timestamps = false;
    protected $table = 'account';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        'permissions',
    ];

//    public function records()
//    {
//        return $this->hasMany('App\Models\RecordRelative', 'uid', 'id');
//    }

    public function getRecords()
    {
        $records = RecordRelative::with('infos')->where('uid', $this->id)->get()->toArray();
        $recordsNew = RecordRelativeNew::with('infos')->where('uid', $this->id)->get()->toArray();
        return array_merge($records, $recordsNew);
        //return $records;
    }
}
