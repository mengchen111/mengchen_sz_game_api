<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TasksPlayer extends Model
{
    public $timestamps = false;
    protected $table = 'tasks_player';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function task()
    {
        return $this->hasOne('App\Models\Tasks', 'id', 'task_id');
    }

    public function player()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid');
    }
}