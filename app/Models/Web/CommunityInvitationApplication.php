<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 *
 * @SWG\Definition(
 *   definition="WebCommunityInvitationApplication",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           description="申请(邀请)记录id",
 *           type="integer",
 *           format="int32",
 *           example=1,
 *       ),
 *       @SWG\Property(
 *           property="player_id",
 *           description="玩家id",
 *           type="integer",
 *           format="int32",
 *           example=10007,
 *       ),
 *       @SWG\Property(
 *           property="type",
 *           description="类型(0-申请,1-邀请)",
 *           type="integer",
 *           format="int32",
 *           example=0,
 *       ),
 *       @SWG\Property(
 *           property="community_id",
 *           description="社团id",
 *           type="integer",
 *           format="int32",
 *           example=10000,
 *       ),
 *       @SWG\Property(
 *           property="status",
 *           description="状态(0-pending,1-approved,2-declined)",
 *           type="integer",
 *           format="int32",
 *           example=1,
 *       ),
 *       @SWG\Property(
 *           property="create_date",
 *           description="创建日期",
 *           type="string",
 *           example="2018-03-30",
 *       ),
 *       allOf={
 *           @SWG\Schema(ref="#/definitions/CreatedAtUpdatedAt"),
 *       }
 * )
 *
 *   * @SWG\Definition(
 *     definition="InvitationApplicationList",
 *     description="牌艺馆成员信息",
 *     allOf={
 *         @SWG\Schema(ref="#/definitions/WebCommunityInvitationApplication"),
 *     },
 *     @SWG\Property(
 *        property="community",
 *        type="object",
 *           allOf={
 *              @SWG\Schema(ref="#/definitions/WebCommunityWithOwnerPlayer"),
 *           },
 *     ),
 * ),
 */
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
