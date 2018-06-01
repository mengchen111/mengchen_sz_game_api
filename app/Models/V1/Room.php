<?php

namespace App\Models\V1;

use App\Traits\RoomPlayers;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @SWG\Definition(
 *   definition="RoomOpenList",
 *   type="object",
 *   @SWG\Property(
 *       property="id",
 *       description="id",
 *       type="integer",
 *       format="int32",
 *       example=583,
 *   ),
 *   @SWG\Property(
 *       property="sid",
 *       description="创建房间的服务器id",
 *       type="integer",
 *       format="int32",
 *       example=145101702963009,
 *   ),
 *   @SWG\Property(
 *       property="ruid",
 *       description="房间唯一标识符",
 *       type="integer",
 *       format="int32",
 *       example=15779909425571445834,
 *   ),
 *   @SWG\Property(
 *       property="rid",
 *       description="房间id",
 *       type="integer",
 *       example=896984,
 *   ),
 *   @SWG\Property(
 *       property="kind",
 *       description="房间类型",
 *       type="integer",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="creator",
 *       description="创建人uid",
 *       type="integer",
 *       format="int32",
 *       example=24330,
 *   ),
 *   @SWG\Property(
 *       property="community",
 *       description="社团id",
 *       type="integer",
 *       format="int32",
 *       example=48696,
 *   ),
 *   @SWG\Property(
 *       property="options",
 *       description="房间选项json",
 *       type="array",
 *       format="int32",
 *       @SWG\Items(
 *          type="integer",
 *       ),
 *      example={123,2,222},
 *   ),
 *   @SWG\Property(
 *       property="rounds",
 *       description="总回合数",
 *       type="integer",
 *       format="int32",
 *       example=6,
 *   ),
 *   @SWG\Property(
 *       property="round",
 *       description="当前回合数",
 *       type="integer",
 *       format="int32",
 *       example=0,
 *   ),
 *  @SWG\Property(
 *       property="players",
 *       description="玩家总人数",
 *       type="integer",
 *       format="int32",
 *       example=4,
 *   ),
 *   @SWG\Property(
 *       property="player",
 *       description="玩家当前人数",
 *       type="integer",
 *       format="int32",
 *       example=3,
 *   ),
 *   @SWG\Property(
 *       property="ctime",
 *       description="房间创建时间",
 *       type="string",
 *       format="int32",
 *       example="2018-05-24 04:15:06",
 *   ),
 *    @SWG\Property(
 *       property="stime",
 *       description="房间回合开始时间",
 *       type="string",
 *       format="int32",
 *       example="2018-05-24 03:07:42",
 *   ),
 *  @SWG\Property(
 *       property="ltime",
 *       description="用来判断次数据是否异常(距离现在超过房间存在时间 2小时)",
 *       type="string",
 *       format="int32",
 *       example="2018-05-24 04:20:00",
 *   ),
 *    @SWG\Property(
 *        property="room_players",
 *        type="object",
 *           allOf={
 *              @SWG\Schema(ref="#/definitions/GamePlayerSimplified"),
 *           },
 *        @SWG\Property(
 *          property="uid",
 *          description="玩家id",
 *          type="integer",
 *          example=22881,
 *        ),
 *        @SWG\Property(
 *          property="score",
 *          description="分数",
 *          type="integer",
 *          example=0,
 *        ),
 *     ),
 * )
 */
class Room extends Model
{
    use RoomPlayers;

    public $timestamps = false;
    protected $appends = [
        'create_date',
    ];

    public function roomPlayers()
    {
        return $this->hasMany(RoomsPlayer::class, 'ruid', 'ruid');
    }

    public function getOptionsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCreateDateAttribute()
    {
        return Carbon::parse($this->attributes['ctime'])->toDateString();
    }

    public function getRuidAttribute($value)
    {
        return (string)$value; //转成字符串，不然20bit位的数字会显示异常
    }
}
