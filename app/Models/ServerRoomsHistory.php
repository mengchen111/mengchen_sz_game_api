<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 *
 * @SWG\Definition(
 *   definition="GameServerRoomsHistory4",
 *   type="object",
 *   @SWG\Property(
 *       property="id",
 *       type="integer",
 *       format="int32",
 *       example=1,
 *   ),
 *   @SWG\Property(
 *       property="ruid",
 *       description="room唯一id",
 *       type="string",
 *       example="1979015395570595113",
 *   ),
 *   @SWG\Property(
 *       property="agent_uid",
 *       description="代理商uid(0为玩家开的房)",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *   @SWG\Property(
 *       property="creator_uid",
 *       description="创房者uid",
 *       type="integer",
 *       format="int32",
 *       example=18618,
 *   ),
 *   @SWG\Property(
 *       property="rid",
 *       description="房间id",
 *       type="integer",
 *       format="int32",
 *       example=305056,
 *   ),
 *   @SWG\Property(
 *       property="rtype",
 *       description="房间类型id(没有数据的时候为0)",
 *       type="string",
 *       example="1979015395570595113",
 *   ),
 *   @SWG\Property(
 *       property="time",
 *       description="创建房间时间",
 *       type="string",
 *       example="2018-03-30 16:03:14",
 *   ),
 *   @SWG\Property(
 *       property="start_time",
 *       description="回合开始时间",
 *       type="string",
 *       example="2018-03-30 16:03:14",
 *   ),
 *   @SWG\Property(
 *       property="end_time",
 *       description="结束时间",
 *       type="string",
 *       example="2018-03-30 16:03:14",
 *   ),
 *   @SWG\Property(
 *       property="options_jstr",
 *       description="游戏选项数据",
 *       type="string",
 *       example="json数据",
 *   ),
 *   @SWG\Property(
 *       property="currency",
 *       description="货币消耗数量",
 *       type="integer",
 *       format="int32",
 *       example=1,
 *   ),
 *   @SWG\Property(
 *       property="max_round",
 *       description="总回合数",
 *       type="integer",
 *       format="int32",
 *       example=8,
 *   ),
 *   @SWG\Property(
 *       property="cur_round",
 *       description="已结束回合数",
 *       type="integer",
 *       format="int32",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="max_cnt",
 *       description="玩家总人数",
 *       type="integer",
 *       format="int32",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="player_cnt",
 *       description="房间当前玩家数",
 *       type="integer",
 *       format="int32",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="uid_1",
 *       description="方位1玩家id",
 *       type="integer",
 *       format="int32",
 *       example=10000,
 *   ),
 *   @SWG\Property(
 *       property="score_1",
 *       description="方位1玩家分数",
 *       type="integer",
 *       format="int32",
 *       example=100,
 *   ),
 *   @SWG\Property(
 *       property="uid_2",
 *       description="方位2玩家id",
 *       type="integer",
 *       format="int32",
 *       example=10001,
 *   ),
 *   @SWG\Property(
 *       property="score_2",
 *       description="方位2玩家分数",
 *       type="integer",
 *       format="int32",
 *       example=-10,
 *   ),
 *   @SWG\Property(
 *       property="uid_3",
 *       description="方位3玩家id",
 *       type="integer",
 *       format="int32",
 *       example=10002,
 *   ),
 *   @SWG\Property(
 *       property="score_3",
 *       description="方位3玩家分数",
 *       type="integer",
 *       format="int32",
 *       example=-40,
 *   ),
 *   @SWG\Property(
 *       property="uid_4",
 *       description="方位4玩家id",
 *       type="integer",
 *       format="int32",
 *       example=10003,
 *   ),
 *   @SWG\Property(
 *       property="score_4",
 *       description="方位4玩家分数",
 *       type="integer",
 *       format="int32",
 *       example=-50,
 *   ),
 *   @SWG\Property(
 *       property="state",
 *       description="标记代理客户端是否显示",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *   @SWG\Property(
 *       property="status",
 *       description="0:正常流程结束房间,1:服务问题",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *   @SWG\Property(
 *       property="community_id",
 *       description="牌艺馆id",
 *       type="integer",
 *       format="int32",
 *       example=10000,
 *   ),
 * )
 *
 */
class ServerRoomsHistory extends Model
{
    public $timestamps = false;
    protected $table = 'server_rooms_history_4';
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

    public function getRecordInfoAttribute()
    {
        //使用like查询为不是relationship是因为未知的原因where查询bigint有时候查询不到
        //return RecordInfosNew::where('ruid', 'like', $this->attributes['ruid'])->first();

        //使用like查询速度超慢，20bit的整型超出了php支持的范围，使用raw查询
        $recordInfo = DB::select('select * from record_infos_new where ruid = ' . (string) $this->attributes['ruid']);
        if (!empty($recordInfo)) {
            $recordInfo[0]->ruid = (string) $recordInfo[0]->ruid;   //转为字符串
            return get_object_vars($recordInfo[0]); //返回数组，而不是stdClass对象
        } else {
            return [];
        }
        //return empty($recordInfo) ? [] : $recordInfo[0];
    }

    public function getRuidAttribute($value)
    {
        return (string) $value; //转成字符串，不然20bit位的数字会显示异常
    }

    public function creator()
    {
        return $this->hasOne('App\Models\Players', 'id', 'creator_uid');
    }

    public function player1()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_1');
    }

    public function player2()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_2');
    }

    public function player3()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_3');
    }

    public function player4()
    {
        return $this->hasOne('App\Models\Players', 'id', 'uid_4');
    }

    public function getKindAttribute()
    {
        return 4;
    }

    public function getRecords()
    {
        //todo 加上kind约束（预留）
        $recordsNew = RecordRelativeNew::with('infos')
            ->where('ruid', $this->ruid)
            ->get()
            ->toArray();
        return $recordsNew;
    }
}
