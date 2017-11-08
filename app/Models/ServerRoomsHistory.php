<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerRoomsHistory extends Model
{
    public $timestamps = false;
    protected $table = 'server_rooms_history_4';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];
}
