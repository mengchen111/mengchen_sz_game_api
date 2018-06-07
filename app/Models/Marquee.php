<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marquee extends Model
{
    protected $table = 'marquee';
    public $timestamps = false;
    protected $fillable = [
        'level', 'content', 'stime', 'etime', 'diff_time', 'status', 'sync',
    ];
}
