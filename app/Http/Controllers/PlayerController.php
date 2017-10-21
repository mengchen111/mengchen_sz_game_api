<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Exceptions\GameServerException;
use App\Http\Requests\ApiRequest;
use App\Models\Players;
use App\Services\Game\GameServer;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Request;
use Exception;

class PlayerController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $players = Players::all();
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    public function search(ApiRequest $request)
    {
        $searchUid = $this->filterRequest($request);

        try {
            $players = Players::where('id', 'like', "%${searchUid}%")->get();
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    protected function filterRequest($request)
    {
        $this->validate($request, [
            'uid' => 'required|numeric',
        ]);
        return $request->uid;
    }

    public function topUp(ApiRequest $request)
    {
        $formData = $this->validateTopUpRequest($request);
        $topUpApi = config('custom.game_server_api_topUp');

        $gameServer = new GameServer();

        try {
            return $gameServer->request('POST', $topUpApi, $formData);
        } catch (GameServerException $e) {
            throw new ApiException($e->getMessage(), $e->getCode());
        }
    }

    protected function validateTopUpRequest($request)
    {
        $this->validate($request, [
            'uid' => 'required|integer',    //玩家ID
            'ctype' => 'required|integer',  //充值类型
            'amount' => 'required|integer', //充值数量
            'timestamp' => 'required|integer'   //时间戳
        ]);

        return $request->intersect([
            'uid', 'ctype', 'amount', 'timestamp',
        ]);
    }
}
