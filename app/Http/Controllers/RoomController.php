<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\RecordInfosNew;
use App\Models\ServerRooms;
use App\Models\ServerRoomsHistory;
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

        ApiLog::add($request);
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
        $data =  $request->all();
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

    public function showOpenRoom(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            //'game_kind' => 'integer', //房间类型(即游戏类型, 惠州麻将、惠东麻将等)
        ]);

        $roomOpened = ServerRooms::when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate('time', $request->input('date'));
        })->when($request->has('game_kind'), function ($query) use ($request) {
            return $query->where('rtype', $request->input('game_kind'));
        })->get();

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => $roomOpened,
        ];
    }

    public function showRoomHistory(ApiRequest $request)
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

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => $roomHistory,
        ];
    }

    //查询社区战绩
    public function searchCommunityRoomRecord(ApiRequest $request)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            'community_id' => 'required|integer',
            'player_id' => 'integer'
        ]);
        $params = $request->only(['start_time', 'end_time', 'community_id', 'player_id']);

        //战绩记录
        $totalUnreadRecordCnt = 0;
        $allRecords = ServerRoomsHistory::with(['creator', 'player1', 'player2', 'player3', 'player4'])
            ->where('currency', '>', 0)     //有过耗卡记录的房间
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
