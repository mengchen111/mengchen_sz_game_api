<?php

namespace App\Models;

use App\Models\Web\CommunityList;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="GamePlayer",
 *   type="object",
 *       @SWG\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 *       @SWG\Property(
 *           property="unionid",
 *           description="玩家微信unionid",
 *           type="string",
 *           example="ope1JuMNKR7NFGEHYxeyfYBb0nQE",
 *       ),
 *       @SWG\Property(
 *           property="nickname",
 *           description="玩家昵称",
 *           type="string",
 *           example="小明",
 *       ),
 *       @SWG\Property(
 *           property="headimg",
 *           description="头像",
 *           type="integer",
 *           format="int32",
 *           example="0",
 *       ),
 *       @SWG\Property(
 *           property="city",
 *           description="城市",
 *           type="string",
 *           example="Shenzhen",
 *       ),
 *       @SWG\Property(
 *           property="gender",
 *           description="性别（1-男,2-女）",
 *           type="integer",
 *           format="int32",
 *           example="1",
 *       ),
 *       @SWG\Property(
 *           property="ycoins",
 *           description="房卡",
 *           type="integer",
 *           format="int32",
 *           example="29",
 *       ),
 *       @SWG\Property(
 *           property="ypoints",
 *           description="金币",
 *           type="integer",
 *           format="int32",
 *           example="0",
 *       ),
 *       @SWG\Property(
 *           property="state",
 *           description="账号当前状态",
 *           type="integer",
 *           format="int32",
 *           example="0",
 *       ),
 *         @SWG\Property(
 *             property="create_time",
 *             description="创建时间",
 *             type="string",
 *             example="2018-03-30 16:03:14",
 *         ),
 *         @SWG\Property(
 *             property="last_time",
 *             description="最近登陆时间",
 *             type="string",
 *             example="2018-03-30 17:14:42",
 *         ),
 *       @SWG\Property(
 *           property="invitation_code",
 *           description="邀请码",
 *           type="integer",
 *           format="int32",
 *           example="10000",
 *       ),
 * )
 *
 */
class Players extends Model
{
    public $timestamps = false;
    public $connection = 'mysql';
    protected $table = 'account';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    protected $hidden = [
        'permissions',
    ];

    protected $appends = [
        //'openid',
    ];

//    public function records()
//    {
//        return $this->hasMany('App\Models\RecordRelative', 'uid', 'id');
//    }

    public function getRecords()
    {
        //不再兼容老版的战绩表（新旧表结构不一致）
        //$records = RecordRelative::with('infos')->where('uid', $this->id)->get()->toArray();
        $recordsNew = RecordRelativeNew::with('infos')->where('uid', $this->id)->get()->toArray();
        //return array_merge($records, $recordsNew);
        return $recordsNew;
    }

    public function getOpenidAttribute()
    {
        $unionidOpenid = UnionidOpenid::find($this->attributes['unionid']);
        return empty($unionidOpenid) ? null : $unionidOpenid->openid;
    }

    //获取玩家所拥有的的社团
    public function getOwnedCommunities()
    {
        return CommunityList::where('owner_player_id', $this->id)
            ->where('status', 1)    //审核已通过的社区
            ->get()
            ->pluck('id')
            ->toArray();
    }

    //获取玩家所属的社团（为社团成员，社团长不算）
    public function getBelongs2Communities()
    {
        $belongs2Communities = [];
        $communities = CommunityList::where('status', 1)
            ->get()
            ->each(function ($item) {
                $item->append('member_ids');
            });
        foreach ($communities as $community) {
            if (in_array($this->id, $community->member_ids)) {
                array_push($belongs2Communities, $community->id);
            }
        }
        return $belongs2Communities;
    }

    public function getInvolvedCommunities()
    {
        $data['owned_communities'] = $this->getOwnedCommunities();
        $data['belongs_to_communities'] = $this->getBelongs2Communities();
        $data['involved_communities'] = array_unique(array_merge($data['owned_communities'],
            $data['belongs_to_communities']));
        return $data;
    }

    //玩家是否是此牌艺馆成员
    public function isMemberOfCommunity(CommunityList $community)
    {
        return in_array($this->id, $community->member_ids);
    }

    //玩家是否是此牌艺馆长
    public function ownedCommunity(CommunityList $community)
    {
        return (int) $this->id === (int) $community->owner_player_id;
    }
}
