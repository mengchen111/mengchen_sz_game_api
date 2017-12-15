<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\ServerRooms;
use App\Models\ServerRoomsHistory;
use Illuminate\Http\Request;
use App\Services\GameServer;
use App\Services\ApiLog;
use Carbon\Carbon;

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
        $data = ServerRooms::all();
        ApiLog::add($request);
        return [
            'result' => true,
            'data' => $data,
        ];
    }

    public function showRoomHistory(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            'game_kind' => 'integer', //房间类型(即游戏类型, 惠州麻将、惠东麻将等)
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
}
