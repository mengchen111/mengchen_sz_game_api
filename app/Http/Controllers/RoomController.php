<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\Services\GameServer;
use App\Services\ApiLog;
use Carbon\Carbon;

class RoomController extends Controller
{
    public function create(Request $request)
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
                'res' => $res,
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
        return $data;
    }
}
