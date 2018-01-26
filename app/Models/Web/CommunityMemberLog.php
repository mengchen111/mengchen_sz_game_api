<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class CommunityMemberLog extends Model
{
    public $connection = 'mysql-web';
    public $timestamps = false;
    protected $table = 'community_member_log';
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
}
