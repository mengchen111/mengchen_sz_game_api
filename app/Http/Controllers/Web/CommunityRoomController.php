<?php

namespace App\Http\Controllers\Web;

use App\Models\Players;
use App\Models\ServerRooms;
use App\Models\V1\Room;
use App\Traits\MaJiangOptionsMap;
use App\Traits\MajiangTypeMap;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityRoomController extends Controller
{
    use MajiangTypeMap;
    use MaJiangOptionsMap;

    /**
     * 获取牌艺馆正在玩的房间信息 - 新版
     * @SWG\Get(
     *     path="/game/community/room/open/{communityId}",
     *     description="获取牌艺馆正在玩的房间信息 - 新版",
     *     operationId="community.room.open.post",
     *     tags={"community"},
     *
     *     @SWG\Parameter(
     *         name="is_full",
     *         description="0-查看未满员，1-查看满员，2-查看所有",
     *         in="path",
     *         required=false,
     *         type="integer",
     *     ),
     *
     *     @SWG\Response(
     *         response=200,
     *         description="入群申请邀请列表",
     *         @SWG\Property(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/Code"),
     *             },
     *             @SWG\Property(
     *                 property="data",
     *                 description="数据",
     *                 type="array",
     *              @SWG\Items(
     *                 type="array",
     *                 @SWG\Items(
     *                    type="object",
     *                     allOf={
     *                        @SWG\Schema(ref="#/definitions/RoomOpenList"),
     *                     },
     *                 ),
     *               ),
     *             ),
     *         ),
     *     ),
     * )
     */
    public function getCommunityOpenRoomV1(Request $request, $communityId, Room $room)
    {
        $this->validate($request, [
            'is_full' => 'required|integer|in:0,1,2', //0-查看未满员，1-查看满员，2-查看所有
        ]);
        $isFull = (int)$request->input('is_full');

//        \DB::connection()->enableQueryLog(); // 开启查询日志
        $communityOpenRooms = $room->where('community', $communityId)
            ->when($isFull !== 2, function ($query) use ($isFull) {
                if ($isFull === 1) {
                    //满员房间
                    return $query->where('player', 4);
                } else {
                    //未满员的房间
                    return $query->where('player', '<', 4);
                }
            })->withPlayers()->orderBy('id', 'desc')->get()->toArray();

//        $queries = \DB::getQueryLog(); // 获取查询日志
//        logger()->info($queries);
        $result = $room->formatRoomData($communityOpenRooms);
        $result = array_chunk($result, 2);   //每个chunk放两个数据给前端

        return [
            'code' => -1,
            'data' => $result,
        ];
    }

    //获取牌艺馆正在玩的房间信息
    public function getCommunityOpenRoom(Request $request, $communityId)
    {
        $this->validate($request, [
            'is_full' => 'required|integer|in:0,1,2', //0-查看未满员，1-查看满员，2-查看所有
        ]);
        $isFull = (int)$request->input('is_full');
        $communityOpenRooms = ServerRooms::where('community_id', $communityId)
            ->when($isFull !== 2, function ($query) use ($isFull) {
                if ($isFull === 1) {    //满员房间
                    return $query->where('player_cnt', 4);
                } else {    //0 为未满员的房间
                    return $query->where('player_cnt', '<', 4);
                }
            })
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $result = $this->formatRoomData($communityOpenRooms);
        $result = array_chunk($result, 2);   //每个chunk放两个数据给前端

        return [
            'code' => -1,
            'data' => $result,
        ];
    }

    protected function formatRoomData($rooms)
    {
        foreach ($rooms as &$room) {
            //$roomOptions = $room['options_jstr'];  //房间玩法
            //unset($room['options_jstr']);     //直接输出玩法key给到游戏前端处理
            //$room['rtype'] = $this->maJiangTypes[$room['rtype']]; //返回给游戏端的rtype不要转
            $room['players'] = [];
            for ($i = 1; $i <= 4; $i++) {
                $tmp = [];
                if ($room['uid_' . $i] != 0) {   //为0表示此座位没人玩，不查询之
                    $player = Players::where('id', $room['uid_' . $i])->first();
                    $tmp['nickname'] = $player['nickname'];
                    $tmp['headimg'] = $player['headimg'];
                }
                $tmp['uid'] = $room['uid_' . $i];
                $tmp['score'] = $room['score_' . $i];
                array_push($room['players'], $tmp);
            }
            //$room['rules'] = $this->getRules($roomOptions);
        }

        return $rooms;
    }

    protected function getRules($options)
    {
        ksort($options);
        $rules = [
            'wanfa' => '',       //玩法
            'gui_pai' => '',    //鬼牌
            'ma_pai' => '',     //马牌
        ];

        array_walk($options, function ($v, $k) use (&$rules) {
            foreach ($this->maJiangOptionsMap as $category => $categoryOptions) {
                if (array_key_exists($k, $categoryOptions)) {
                    if ((!empty($v)) or $k == 16) {    //无鬼补花类型值可能为0
                        if (is_array($categoryOptions[$k])) {
                            $rules[$category] .= "{$categoryOptions[$k]['name']}: {$categoryOptions[$k]['options'][$v]},";
                        } else {
                            if ($category === 'ma_pai') {
                                $rules[$category] .= "{$categoryOptions[$k]}: $v,";      //买了多少马
                            } elseif ($k === 26) {
                                $rules[$category] .= "{$categoryOptions[$k]}: $v,";      //底分多少
                            } else {
                                $rules[$category] .= "{$categoryOptions[$k]},";
                            }
                        }
                    }
                }
            }
        });

        return $rules;
    }
}
