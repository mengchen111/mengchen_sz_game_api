<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

class RecordRelative extends Model
{
    public $timestamps = false;
    protected $table = 'record_relative';
    protected $primaryKey = 'id';

    public function infos()
    {
        return $this->belongsTo(Record::class, 'rec_id', 'id');
    }
}
