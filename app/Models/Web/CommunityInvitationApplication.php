<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class CommunityInvitationApplication extends Model
{
    public $connection = 'mysql-web';
    public $timestamps = true;
    protected $table = 'community_invitation_application';
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
