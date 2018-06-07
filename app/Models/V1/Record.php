<?php

namespace App\Models\V1;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="GameRecordInfoV1",
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
 *       example=5044,
 *   ),
 *   @SWG\Property(
 *       property="community_id",
 *       description="俱乐部id",
 *       type="integer",
 *       example=71701,
 *   ),
 *   @SWG\Property(
 *       property="kind",
 *       description="俱乐部id",
 *       type="integer",
 *       example=267,
 *   ),
 *   @SWG\Property(
 *       property="ctime",
 *       description="创建时间",
 *       type="string",
 *       example="2018-06-06 02:09:38",
 *   ),
 *   @SWG\Property(
 *       property="stime",
 *       description="开始时间",
 *       type="string",
 *       example="2018-06-06 02:10:22",
 *   ),
 *  @SWG\Property(
 *       property="etime",
 *       description="结束时间",
 *       type="string",
 *       example="2018-06-06 03:01:40",
 *   ),
 *   @SWG\Property(
 *       property="itime",
 *       description="插入记录时间",
 *       type="string",
 *       example="2018-06-06 03:01:41",
 *   ),
 *   @SWG\Property(
 *       property="if_read",
 *       description="牌艺馆长是否已查看(0-未查看,1-已查看)",
 *       type="integer",
 *       format="int32",
 *       example=1,
 *   ),
 * )
 *
 */
class Record extends Model
{
    public $timestamps = false;

    public function getJstrAttribute($value)
    {
        return mb_convert_encoding($value, 'UTF-8');
    }

    public function getRuidAttribute($value)
    {
        return (string) $value; //转成字符串，不然前端读取有问题
    }
}
