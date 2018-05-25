<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public $timestamps = false;

    public function getJstrAttribute($value)
    {
        return mb_convert_encoding($value, 'UTF-8');
    }

    public function getRuidAttribute($value)
    {
        return (string) $value; //转成字符串，不然前端读取有问题
    }
}
