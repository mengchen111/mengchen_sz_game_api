<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordRelativeNew extends Model
{
    public $timestamps = false;
    protected $table = 'record_relative_new';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    public function infos()
    {
        return $this->hasOne('App\Models\RecordInfosNew', 'id', 'rec_id');
    }
}
