<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordInfos extends Model
{
    public $timestamps = false;
    protected $table = 'record_infos';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    public function getRecJstrAttribute($value)
    {
        return mb_convert_encoding($value, 'UTF-8');
    }
}
