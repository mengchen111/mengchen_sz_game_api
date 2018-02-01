<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    protected $appends = [
        'create_date',
    ];

    public function community()
    {
        return $this->hasOne('App\Models\Web\CommunityList', 'id', 'community_id');
    }

    public function getCreateDateAttribute()
    {
        $date = Carbon::parse($this->attributes['created_at'])->toDateString();
        return $date;
    }
}
