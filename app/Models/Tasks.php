<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    public $timestamps = false;
    protected $table = 'tasks';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function typeModel()
    {
        return $this->hasOne('App\Models\TaskType', 'id', 'type');
    }
}