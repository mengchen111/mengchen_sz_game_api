<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="GameRecordInfo",
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
 *       property="rec_jstr",
 *       description="战绩详情数据",
 *       type="string",
 *       example="json数据",
 *   ),
 *   @SWG\Property(
 *       property="ins_time",
 *       description="插入数据时间",
 *       type="string",
 *       example="2018-03-30 16:03:14",
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
class RecordInfosNew extends Model
{
    public $timestamps = false;
    protected $table = 'record_infos_new';
    protected $primaryKey = 'id';

    protected $fillable = [
        //只读
    ];

    public function getRecJstrAttribute($value)
    {
        return mb_convert_encoding($value, 'UTF-8');
    }

    public function getRuidAttribute($value)
    {
        return (string) $value; //转成字符串，不然前端读取有问题
    }
}
