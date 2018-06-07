<?php

namespace App\Models\V1;

use App\Models\Players;
use App\Traits\RoomPlayers;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="RoomsHistory",
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
 *       property="rid",
 *       description="房间id",
 *       type="integer",
 *       format="int32",
 *       example=855563,
 *   ),
 *   @SWG\Property(
 *       property="kind",
 *       description="房间类型",
 *       type="integer",
 *       format="int32",
 *       example=267,
 *   ),
 *   @SWG\Property(
 *       property="ctype",
 *       description="创建房间类型 0:普通房 1:代开房",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *   @SWG\Property(
 *       property="community",
 *       description="社团ID",
 *       type="integer",
 *       example=71701,
 *   ),
 *   @SWG\Property(
 *       property="rounds",
 *       description="总回合数",
 *       type="integer",
 *       example=8,
 *   ),
 *   @SWG\Property(
 *       property="round",
 *       description="当前回合数",
 *       type="integer",
 *       example=2,
 *   ),
 *   @SWG\Property(
 *       property="players",
 *       description="玩家总人数",
 *       type="integer",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="player",
 *       description="玩家当前人数",
 *       type="integer",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="ctime",
 *       description="创建时间",
 *       type="string",
 *       example="2018-06-06 02:54:18",
 *   ),
 *   @SWG\Property(
 *       property="stime",
 *       description="回合开始时间",
 *       type="string",
 *       example="2018-06-06 03:02:57",
 *   ),
 *   @SWG\Property(
 *       property="etime",
 *       description="结束时间",
 *       type="string",
 *       example="2018-06-06 03:07:27",
 *   ),
 *   @SWG\Property(
 *       property="cost",
 *       description="房卡消耗",
 *       type="integer",
 *       format="int32",
 *       example=1,
 *   ),
 *   @SWG\Property(
 *       property="state",
 *       description="标记代理客户端是否显示, 0:显示 1:不显示",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *   @SWG\Property(
 *       property="status",
 *       description="0:正常流程结束房间 1:服务问题",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 * )
 *
 */
class RoomsHistory extends Model
{
    use RoomPlayers;

    protected $table = 'rooms_history';
    public $timestamps = false;

    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }

    public function creator()
    {
        return $this->belongsTo(Players::class, 'creator');
    }

    public function roomPlayers()
    {
        return $this->hasMany(RoomsHistoryPlayer::class, 'ruid', 'ruid');
    }

    public function recordsInfo()
    {
        return $this->belongsTo(Record::class, 'ruid', 'ruid');
    }

    public function getRecords()
    {
        //todo 加上kind约束（预留）
        $records = RecordRelative::with('infos')
            ->where('ruid', $this->ruid)
            ->get()
            ->toArray();

        return $records;
    }

}
