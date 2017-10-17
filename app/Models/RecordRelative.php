<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordRelative extends Model
{
    public $timestamps = false;
    protected $table = 'record_relative';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    public function infos()
    {
        return $this->hasOne('App\Models\RecordInfos', 'id', 'rec_id');
    }
}
