<?php

namespace App\Models;

use Carbon\Carbon;
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

    protected $appends = [
        'create_date',
    ];

    public function getOptionsJstrAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCreateDateAttribute()
    {
        $date = Carbon::parse($this->attributes['time'])->toDateString();
        return $date;
    }
}
