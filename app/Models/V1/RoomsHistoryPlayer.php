<?php

namespace App\Models\V1;

use App\Models\Players;
use Illuminate\Database\Eloquent\Model;

class RoomsHistoryPlayer extends Model
{
    public $timestamps = false;
    protected $table = 'rooms_history_player';
    protected $primaryKey = 'ruid';

    public function player()
    {
        return $this->belongsTo(Players::class, 'uid');
    }
}
