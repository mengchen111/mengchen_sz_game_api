<?php

namespace App\Models\Web;

use App\Models\Players;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 *
 * @SWG\Definition(
 *   definition="WebCommunity",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           description="社团id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="owner_agent_id",
 *           description="社团所有者代理商id",
 *           type="integer",
 *           format="int32",
 *           example="520",
 *       ),
 *       @SWG\Property(
 *           property="owner_player_id",
 *           description="社团所有者玩家id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="name",
 *           description="社团名称",
 *           type="string",
 *           example="社团aaa",
 *       ),
 *       @SWG\Property(
 *           property="info",
 *           description="社团简介",
 *           type="string",
 *           example="这是社团简介",
 *       ),
 *       @SWG\Property(
 *           property="card_stock",
 *           description="社团房卡库存",
 *           type="integer",
 *           format="int32",
 *           example="77",
 *       ),
 *       @SWG\Property(
 *           property="card_frozen",
 *           description="社团房卡冻结数量",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="status",
 *           description="社团状态(0-待审核,1-审核通过,2-审核不通过)",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="members_count",
 *           description="成员数量",
 *           type="integer",
 *           format="int32",
 *           example="6",
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
 */
class CommunityList extends Model
{
    public $connection = 'mysql-web';
    public $timestamps = true;
    protected $table = 'community_list';
    protected $primaryKey = 'id';

    protected $guarded = [
        //
    ];

    protected $hidden = [
        //
    ];

    protected $appends = [
        'members_count', 'create_date'
    ];

    public function getCreateDateAttribute()
    {
        $date = Carbon::parse($this->attributes['created_at'])->toDateString();
        return $date;
    }

    public function ownerPlayer()
    {
        return $this->hasOne('App\Models\Players', 'id', 'owner_player_id');
    }

    public function getMembersCountAttribute()
    {
        $members = $this->attributes['members'];
        return empty($members) ? 0 : count(explode(',', $members));
    }

    /**
     * @return array
     *
     * 将成员信息解构，然后获取成员的基本信息(头像，昵称和id)
     *
     * @SWG\Definition(
     *     definition="CommunityMemberInfo",
     *     description="牌艺馆成员信息",
     *     type="object",
     *     @SWG\Property(
     *         property="id",
     *         description="玩家id",
     *         type="integer",
     *         format="int32",
     *         example="11000",
     *     ),
     *     @SWG\Property(
     *         property="nickname",
     *         description="玩家昵称",
     *         type="string",
     *         example="文德-泽?",
     *     ),
     *     @SWG\Property(
     *         property="headimg",
     *         description="玩家头像地址",
     *         type="string",
     *         example="http://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ervXvnC9rIx0cRxVibY8pU3sh2pVI4lkF4dwtxnoxZyRqZPV3icicBx7Nq7zJxjiaQfejVr0EJF3ia1Ricg/132",
     *     ),
     * ),
     */
    public function getMembersInfoAttribute()
    {
        $remainedPlayerInfo = ['id', 'nickname', 'headimg'];    //只显示这些玩家信息
        $returnData = [];
        $members = $this->member_ids;

        if (empty($members)) {
            $returnData = [];
        } else {
            Players::whereIn('id', $members)
                ->get()
                ->each(function ($item) use (&$returnData, $remainedPlayerInfo) {
                    $player = $item->setVisible($remainedPlayerInfo)->toArray();
                    array_push($returnData, $player);
                });
        }

        return $returnData;
    }

    //获取此社区的申请列表
    public function getApplicationDataAttribute()
    {
        $applicationData = [];
        $applications = CommunityInvitationApplication::where('community_id', $this->attributes['id'])
            ->where('type', 0)  //类型为申请
            ->where('status', 0)    //状态为pending
            ->get();
        $applicationData['application_count'] = $applications->count(); //申请数量

        $remainedPlayerInfo = ['id', 'nickname', 'headimg'];    //只显示这些玩家信息
        foreach ($applications as &$application) {
            $player = PlayerService::findPlayer($application->player_id);
            $player = collect($player)->only($remainedPlayerInfo)->toArray();
            $application['player'] = $player;   //添加申请者的基本信息
        }
        $applicationData['applications'] = $applications;   //申请信息

        return $applicationData;
    }

    //社区动态列表
    public function getMemberLogAttribute()
    {
        $memberLogs = CommunityMemberLog::where('community_id', $this->attributes['id'])
            ->orderBy('id', 'desc')
            ->limit(10)     //只显示10条最新动态
            ->get();
        $remainedPlayerInfo = ['id', 'nickname'];    //只显示这些玩家信息
        foreach ($memberLogs as $memberLog) {
            $player = PlayerService::findPlayer($memberLog->player_id);
            $player = collect($player)->only($remainedPlayerInfo)->toArray();
            $memberLog['player'] = $player;
        }
        return $memberLogs;
    }

    //获取成员id的数组列表
    public function getMemberIdsAttribute()
    {
        return explode(',', $this->attributes['members']);
    }

    public function addMembers(Array $newMembers)
    {
        if (empty($this->members)) {
            $existMembers = [];
        } else {
            $existMembers = explode(',', $this->members);
        }
        foreach ($newMembers as $newMember) {
            if (!in_array($newMembers, $existMembers)) {
                array_push($existMembers, $newMember);
            }
        }
        $this->members = implode(',', $existMembers);
        $this->save();
    }

    public function deleteMembers(Array $abandonedMembers)
    {
        if (empty($this->members)) {
            $existMembers = [];
        } else {
            $existMembers = explode(',', $this->members);
        }
        foreach ($abandonedMembers as $abandonedMember) {
            if (in_array($abandonedMember, $existMembers)) {
                unset($existMembers[array_search($abandonedMember, $existMembers)]);
            }
        }
        $this->members = implode(',', $existMembers);
        $this->save();
    }

    //检查成员是否存在此群中
    public function ifHasMember($playerId)
    {
        $existMembers = explode(',', $this->members);
        return in_array($playerId, $existMembers);
    }

    //检查社团名是否有重复
    public function ifNameDuplicated($communityName)
    {
        $allNames = CommunityList::where('id', '!=', $this->id)
            ->select('name')
            ->get()
            ->pluck('name');
        return $allNames->search($communityName);
    }
}
