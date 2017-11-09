<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerRooms extends Model
{
    public $timestamps = false;
    protected $table = 'server_rooms_4';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        //
    ];

    public function getOptionsJstrAttribute($value)
    {
        return json_decode($value, true);
    }
}
