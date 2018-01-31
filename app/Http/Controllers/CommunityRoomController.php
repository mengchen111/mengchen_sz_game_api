<?php

namespace App\Http\Controllers;

use App\Models\Players;
use App\Models\ServerRooms;
use App\Traits\MaJiangOptionsMap;
use App\Traits\MajiangTypeMap;
use Illuminate\Http\Request;

class CommunityRoomController extends Controller
{
    use MajiangTypeMap;
    use MaJiangOptionsMap;

    //获取牌艺馆正在玩的房间信息
    public function getCommunityOpenRoom(Request $request)
    {
        $this->validate($request, [
            'community_id' => 'required|integer',
            'is_full' => 'required|integer|in:0,1,2', //0-查看未满员，1-查看满员，2-查看所有
        ]);
        $isFull = (int)$request->input('is_full');
        $communityId = $request->input('community_id');
        $communityOpenRooms = ServerRooms::where('community_id', $communityId)
            ->when($isFull !== 2, function ($query) use ($isFull) {
                if ($isFull === 1) {    //满员房间
                    return $query->where('player_cnt', 4);
                } else {    //0 为满员的房间
                    return $query->where('player_cnt', '<', 4);
                }
            })
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        $result = $this->formatRoomData($communityOpenRooms);

        return [
            'result' => 'true',
            'data' => $result,
        ];
    }

    protected function formatRoomData($rooms)
    {
        foreach ($rooms as &$room) {
            //$roomOptions = $room['options_jstr'];  //房间玩法
            unset($room['options_jstr']);     //返回给web后端的接口无需显示options
            $room['rtype'] = $this->maJiangTypes[$room['rtype']];
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
                    if ((! empty($v)) or $k == 16) {    //无鬼补花类型值可能为0
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
