<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class RecordRelative extends Model
{
    public $timestamps = false;
    protected $table = 'record_relative';
    protected $primaryKey = 'id';

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }
    public function infos()
    {
        return $this->belongsTo(Record::class, 'rec_id', 'id');
    }
}
