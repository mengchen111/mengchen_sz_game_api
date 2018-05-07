<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @SWG\Definition(
 *   definition="WebCommunityMemberLog",
 *   type="object",
 *   @SWG\Property(
 *       property="id",
 *       description="社团动态id",
 *       type="integer",
 *       format="int32",
 *       example=1,
 *   ),
 *   @SWG\Property(
 *       property="community_id",
 *       description="社团id",
 *       type="integer",
 *       format="int32",
 *       example=10000,
 *   ),
 *   @SWG\Property(
 *       property="player_id",
 *       description="玩家id",
 *       type="integer",
 *       format="int32",
 *       example=10000,
 *   ),
 *   @SWG\Property(
 *       property="action",
 *       description="操作",
 *       type="string",
 *       example="加入",
 *   ),
 *   @SWG\Property(
 *       property="create_at",
 *       description="创建日期",
 *       type="string",
 *       example="2018-03-30 17:54:12",
 *   ),
 * )
 *
 */
class CommunityMemberLog extends Model
{
    public $connection = 'mysql-web';
    public $timestamps = false;
    protected $table = 'community_member_log';
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
