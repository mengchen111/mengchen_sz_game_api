<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class CommunityCardTopupLog extends Model
{
    public $connection = 'mysql-web';
    public $timestamps = false;
    protected $table = 'community_card_topup_log';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    public function community()
    {
        return $this->hasOne('App\Models\Web\CommunityList', 'id', 'community_id');
    }

    public function item()
    {
        return $this->hasOne('App\Models\Web\ItemType', 'id', 'item_type_id');
    }
}
