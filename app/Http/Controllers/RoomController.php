<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\RecordInfosNew;
use App\Models\ServerRooms;
use App\Models\ServerRoomsHistory;
use App\Models\V1\Room;
use App\Models\V1\RoomsHistory;
use Illuminate\Http\Request;
use App\Services\GameServer;
use App\Services\ApiLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function create(ApiRequest $request)
    {
        $formData = $this->validateRoomCreateData($request);
        $roomCreateApi = config('custom.game_server_api_roomCreate');

        $gameServer = new GameServer();
        $res = $gameServer->request('POST', $roomCreateApi, $formData);

        return [
            'result' => true,
            'data' => [
                'message' => '创建房间成功',
                'room_id' => $res['info'],
            ],
        ];
    }

    protected function validateRoomCreateData($request)
    {
        $this->validate($request, [
            'creator' => 'integer',
        ]);
        $data = $request->all();
        $data['timestamp'] = Carbon::now()->timestamp;

        //网站传过来的api_key和sign参数去掉
        if (isset($data['api_key'])) {
            unset($data['api_key']);
        }
        if (isset($data['sign'])) {
            unset($data['sign']);
        }

        return $data;
    }

    public function showOpenRoom(ApiRequest $request, Room $room)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            //'game_kind' => 'integer', //房间类型(即游戏类型, 惠州麻将、惠东麻将等)
        ]);

//        $roomOpened = ServerRooms::when($request->has('date'), function ($query) use ($request) {
//            return $query->whereDate('time', $request->input('date'));
//        })->when($request->has('game_kind'), function ($query) use ($request) {
//            return $query->where('rtype', $request->input('game_kind'));
//        })->get();

        // 新版
        $roomOpened = $room->when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate('ctime', $request->input('date'));
        })->when($request->has('game_kind'), function ($query) use ($request) {
            return $query->where('kind', $request->input('game_kind'));
        })->withPlayers()->get()->toArray();
        $roomOpened = $room->formatRoomData($roomOpened);

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => $roomOpened,
        ];
    }

    public function showRoomHistory(ApiRequest $request, RoomsHistory $history)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            //'game_kind' => 'integer', //房间类型(即游戏类型, 惠州麻将、惠东麻将等)
        ]);
        $roomHistory = ServerRoomsHistory::when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate('time', $request->input('date'));
        })->when($request->has('game_kind'), function ($query) use ($request) {
            return $query->where('rtype', $request->input('game_kind'));
        })->get();

        //新版
//        $roomHistory = $history->when($request->has('date'), function ($query) use ($request) {
//            return $query->whereDate('ctime', $request->input('date'));
//        })->when($request->has('game_kind'), function ($query) use ($request) {
//            return $query->where('kind', $request->input('game_kind'));
//        })->withPlayers()->latest('etime')->paginate($this->page);
//
//        $roomHistory = $history->formatRoomData($roomHistory);

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => $roomHistory,
        ];
    }

    public function searchCommunityRoomRecordV1(Request $request)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            'community_id' => 'required|integer',
            'player_id' => 'integer',
        ]);
        $params = $request->only(['start_time', 'end_time', 'community_id', 'player_id']);
//        \DB::connection()->enableQueryLog(); // 开启查询日志
        //战绩记录
        $totalUnreadRecordCnt = 0;
        $allRecords = RoomsHistory::with(['creator','recordsInfo','roomPlayers.player'])
            ->where('cost', '>', 0)//有过耗卡记录的房间
            ->whereBetween('ctime', [$params['start_time'], $params['end_time']])
            ->where('community', $params['community_id'])
            ->get()
            ->each(function ($item) use (&$totalUnreadRecordCnt) {
                //计算未读战绩总数
                if (!empty($item['record_info'])) {
                    if ((int)$item['record_info']['if_read'] === 0) {
                        $totalUnreadRecordCnt += 1;
                    }
                }
            })
            ->toArray();
//        $queries = \DB::getQueryLog(); // 获取查询日志
//        logger()->info($queries);
        $result['records'] = $this->formatRecord($allRecords,$params);
        $result['total_unread_record_cnt'] = $totalUnreadRecordCnt;
        return [
            'result' => true,
            'data' => $result,
        ];
    }

    public function formatRecord($records,$params)
    {
        $player = [];
        foreach ($records as $k => &$record) {
            $playerIds = [];
            // 格式转换为原来接口的格式
            if (!empty($record['room_players'])){
                foreach ($record['room_players'] as $key => $item){
                    $playerIds[] = $item['uid'];
                    if (!empty($item['player'])){
                        $loop = $key + 1;
                        $player['player'.$loop] = $item['player'];
                        $player['player'.$loop]['score'] = $item['score'];
                    }
                }
            }
            //判断 用户id 是否在玩家数组中 ，不在则过滤删除
            if ($params['player_id'] && !in_array($params['player_id'],$playerIds)){
                unset($records[$k]);
                continue;
            }
            unset($record['room_players']);
            $records[$k] = array_merge($records[$k],$player);
        }
        return $records;
    }
    //查询社区战绩
    public function searchCommunityRoomRecord(Request $request)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            'community_id' => 'required|integer',
            'player_id' => 'integer',
        ]);
        $params = $request->only(['start_time', 'end_time', 'community_id', 'player_id']);

        //战绩记录
        $totalUnreadRecordCnt = 0;
        $allRecords = ServerRoomsHistory::with(['creator', 'player1', 'player2', 'player3', 'player4'])
            ->where('currency', '>', 0)//有过耗卡记录的房间
            ->whereBetween('time', [$params['start_time'], $params['end_time']])
            ->where('community_id', $params['community_id'])
            ->when($request->has('player_id'), function ($query) use ($params) {
                return $query->where([
                    ['uid_1', '=', $params['player_id']],
                    ['uid_2', '=', $params['player_id'], 'or'],
                    ['uid_3', '=', $params['player_id'], 'or'],
                    ['uid_4', '=', $params['player_id'], 'or'],
                ]);
            })
            ->orderBy('id', 'desc')
            ->get()
            ->each(function ($item) use (&$totalUnreadRecordCnt) {
                $item->append('record_info');
                //计算未读战绩总数
                if (!empty($item['record_info'])) {
                    if ((int)$item['record_info']['if_read'] === 0) {
                        $totalUnreadRecordCnt += 1;
                    }
                }
            });

//        $records = $allRecords->filter(function ($item) use ($params) {
//            return $params['start_time'] <= $item['time'] && $item['time'] <= $params['end_time'];
//        });

        $result['total_unread_record_cnt'] = $totalUnreadRecordCnt;
        $result['records'] = $allRecords;

        return [
            'result' => true,
            'data' => $result,
        ];
    }

    //标记战绩为已读/未读
    public function markRecord(ApiRequest $request)
    {
        $this->validate($request, [
            'record_info_id' => 'required|integer',
            'if_read' => 'required|integer|in:0,1',
        ]);

        $record = RecordInfosNew::findOrFail($request->input('record_info_id'));
        $record->if_read = $request->input('if_read');
        $record->save();


        return [
            'result' => true,
            'data' => '战绩标记成功',
        ];
    }
}
