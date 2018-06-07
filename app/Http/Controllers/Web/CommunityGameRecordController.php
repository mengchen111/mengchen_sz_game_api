<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ApiRequest;
use App\Traits\ApiRequestResolver;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CommunityGameRecordController extends Controller
{
    use ApiRequestResolver;
    /**
     * 新版 - 查询牌艺馆战绩
     * @SWG\Get(
     *     path="/game/v1/community/game-record/{communityId}",
     *     description="新版 - 查询牌艺馆战绩",
     *     operationId="v1.community.game-record.get",
     *     tags={"v1"},
     *
     *     @SWG\Parameter(
     *         name="communityId",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="start_time",
     *         description="开始时间",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="end_time",
     *         description="结束时间",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="player_id",
     *         description="玩家id",
     *         in="query",
     *         required=false,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="战绩数据",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="unread_records",
     *                     description="当前查询未读战绩数",
     *                     type="integer",
     *                     format="int32",
     *                     example=2,
     *                 ),
     *                 @SWG\Property(
     *                     property="total_unread_record_cnt",
     *                     description="此牌艺馆所有未读战绩数",
     *                     type="integer",
     *                     format="int32",
     *                     example=10,
     *                 ),
     *                 @SWG\Property(
     *                     property="records",
     *                     description="战绩详情",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="object",
     *                         allOf={
     *                             @SWG\Schema(ref="#/definitions/RoomsHistory"),
     *                         },
     *                         @SWG\Property(
     *                             property="records_info",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GameRecordInfoV1"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="creator",
     *                             description="房主信息",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="player1",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                             @SWG\Property(
     *                                  property="score",
     *                                  description="分数",
     *                                  type="integer",
     *                                  example=5,
     *                               ),
     *                              @SWG\Property(
     *                                  property="is_witter",
     *                                  description="是否赢家",
     *                                  type="boolean",
     *                                  example=true,
     *                               ),
     *                         ),
     *                         @SWG\Property(
     *                             property="player2",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                            @SWG\Property(
     *                                  property="score",
     *                                  description="分数",
     *                                  type="integer",
     *                                  example=5,
     *                               ),
     *                              @SWG\Property(
     *                                  property="is_witter",
     *                                  description="是否赢家",
     *                                  type="boolean",
     *                                  example=true,
     *                               ),
     *                         ),
     *                         @SWG\Property(
     *                             property="player3",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                            @SWG\Property(
     *                                  property="score",
     *                                  description="分数",
     *                                  type="integer",
     *                                  example=5,
     *                               ),
     *                              @SWG\Property(
     *                                  property="is_witter",
     *                                  description="是否赢家",
     *                                  type="boolean",
     *                                  example=true,
     *                               ),
     *                         ),
     *                         @SWG\Property(
     *                             property="player4",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                            @SWG\Property(
     *                                  property="score",
     *                                  description="分数",
     *                                  type="integer",
     *                                  example=5,
     *                               ),
     *                              @SWG\Property(
     *                                  property="is_witter",
     *                                  description="是否赢家",
     *                                  type="boolean",
     *                                  example=true,
     *                               ),
     *                         ),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function searchV1(Request $request, $communityId)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            //'community_id' => 'required|integer',
            'player_id' => 'integer'
        ]);
        $params = $request->intersect(['start_time', 'end_time', 'player_id']);
        $params['community_id'] = $communityId;

        $records = $this->callController('App\Http\Controllers\RoomController@searchCommunityRoomRecordV1'
            , $params, $request->getMethod());

        $result = $this->formatRecordsV1($records);

        return $this->res($result);
    }
    protected function formatRecordsV1($records)
    {
        $count = 0;
        foreach ($records['records'] as &$record) {
            unset($record['options']); //不显示玩法详情
            if (!empty($record['records_info'])) {
                unset($record['records_info']['jstr']);  //不现实战绩详情
                if ((int)$record['records_info']['if_read'] === 0) {
                    $count += 1;
                }
            }
            // winter 最大赢家
            foreach ($records['records'] as &$item){
                $score = 0;
                // 初始化赋值为 false , 并找出最大的分值
                for ($i = 1; $i <= $item['player']; $i++){
                    $item['player'.$i]['is_witter'] = false;
                    if ($item['player'.$i]['score'] > $score){
                        $score = $item['player'.$i]['score'];
                    }
                }
                //根据最大分值进行赋值 true
                for ($i = 1; $i <= $item['player']; $i++){
                    if ($item['player'.$i]['score'] == $score){
                        $item['player'.$i]['is_witter'] = true;
                    }
                }
            }
        }
        $result['unread_records'] = $count;
        $result['records'] = $records['records'];
        $result['total_unread_record_cnt'] = $records['total_unread_record_cnt'];
        return $result;
    }
    /**
     * 旧版 - 查询牌艺馆战绩
     * @SWG\Get(
     *     path="/game/community/game-record/{communityId}",
     *     description="旧版 - 查询牌艺馆战绩",
     *     operationId="community.game-record.get",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="communityId",
     *         description="社团id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         name="start_time",
     *         description="开始时间",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="end_time",
     *         description="结束时间",
     *         in="query",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         name="player_id",
     *         description="玩家id",
     *         in="query",
     *         required=false,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="战绩数据",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="object",
     *                 @SWG\Property(
     *                     property="unread_records",
     *                     description="当前查询未读战绩数",
     *                     type="integer",
     *                     format="int32",
     *                     example=2,
     *                 ),
     *                 @SWG\Property(
     *                     property="total_unread_record_cnt",
     *                     description="此牌艺馆所有未读战绩数",
     *                     type="integer",
     *                     format="int32",
     *                     example=10,
     *                 ),
     *                 @SWG\Property(
     *                     property="records",
     *                     description="战绩详情",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="object",
     *                         allOf={
     *                             @SWG\Schema(ref="#/definitions/GameServerRoomsHistory4"),
     *                         },
     *                         @SWG\Property(
     *                             property="record_info",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GameRecordInfo"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="creator",
     *                             description="房主信息",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="player1",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="player2",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="player3",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                         @SWG\Property(
     *                             property="player4",
     *                             type="object",
     *                             allOf={
     *                                 @SWG\Schema(ref="#/definitions/GamePlayer"),
     *                             },
     *                         ),
     *                     ),
     *                 ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function search(Request $request, $communityId)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            //'community_id' => 'required|integer',
            'player_id' => 'integer'
        ]);
        $params = $request->intersect(['start_time', 'end_time', 'player_id']);
        $params['community_id'] = $communityId;

        $records = $this->callController('App\Http\Controllers\RoomController@searchCommunityRoomRecord'
            , $params, $request->getMethod());
        $records['records'] = $records['records']->toArray();   //格式化成数组，防止报错

        $result = $this->formatRecords($records);

        return $this->res($result);
    }

    protected function formatRecords($records)
    {
        $count = 0;
        foreach ($records['records'] as &$record) {
            unset($record['options_jstr']); //不显示玩法详情
            if (!empty($record['record_info'])) {
                unset($record['record_info']['rec_jstr']);  //不现实战绩详情
                if ((int)$record['record_info']['if_read'] === 0) {
                    $count += 1;
                }
            }
            //大赢家（得分最高者）标识
            array_reduce([2,3,4], function (Array $v1, $v2) use (&$record) {
                //$record['player' . $v2]['is_winner'] = true;    //先将第一个玩家设置为赢家
                if ($record['score_' . $v1[0]] < $record['score_' . $v2]) {
                    //只有当第二个玩家的分数大于第一个玩家的时候才将第二个玩家设置为大赢家
                    $record['player' . $v2]['is_winner'] = true;
                    //将第一个玩家赢家标识取消(可能存在多个大赢家)
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = false;
                    });
                    return [$v2];
                } elseif ($record['score_' . $v1[0]] === $record['score_' . $v2]) {
                    $record['player' . $v2]['is_winner'] = true;
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = true;
                    });
                    array_push($v1, $v2);
                    return $v1;
                } else {
                    $record['player' . $v2]['is_winner'] = false;
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = true;
                    });
                    return $v1;
                }
            }, [1]);
        }
        $result['unread_records'] = $count;
        $result['records'] = $records['records'];
        $result['total_unread_record_cnt'] = $records['total_unread_record_cnt'];
        return $result;
    }

    /**
     * 标记战绩为已读
     * @SWG\Post(
     *     path="/game/community/game-record/mark/{recordInfoId}",
     *     description="标记战绩为已读",
     *     operationId="community.game-record.mark.put",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="recordInfoId",
     *         description="record_info_id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=404,
     *         description="未知的record_info_id",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="标记成功",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Success"),
     *             },
     *         ),
     *     ),
     * )
     */
    public function markRecord(Request $request, $recordInfoId)
    {
        Validator::make($request->route()->parameters(), [
            'recordInfoId' => 'required|integer|exists:record_infos_new,id'
        ]);

        $params['record_info_id'] = $recordInfoId;
        $params['if_read'] = 1;
        $result = $this->callController('App\Http\Controllers\RoomController@markRecord'
            ,$params, $request->getMethod());

        return $this->res($result);
    }
}
