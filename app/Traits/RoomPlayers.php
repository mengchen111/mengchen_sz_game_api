<?php
/**
 * Created by PhpStorm.
 * User: wangjun
 * Date: 2018/5/25
 * Time: 11:45
 */

namespace App\Traits;

trait RoomPlayers
{
    /**
     * 关联
     * @param $query
     * @return mixed
     */
    public function scopeWithPlayers($query)
    {
        return $query->with(['roomPlayers' => function ($query) {
            return $query->with(['player' => function ($query) {
                return $query->select('id', 'nickname', 'headimg');
            }]);
        }]);
    }

    /**
     * 转换格式
     * @param $rooms
     * @return mixed
     */
    public function formatRoomData($rooms)
    {
        foreach ($rooms as $key => $room) {
            if (is_array($room['room_players']) && !empty($room['room_players'])) {
                $data = [];
                foreach ($room['room_players'] as $player) {
                    $data[] = [
                        'cid' => $player['cid'],
                        'uid' => $player['uid'],
                        'score' => $player['score'],
                        'nickname' => $player['player']['nickname'],
                        'headimg' => $player['player']['headimg'],
                    ];
                }
                $rooms[$key]['room_players'] = $data;
            }
        }
        return $rooms;
    }
}