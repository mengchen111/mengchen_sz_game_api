<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Exceptions\GameServerException;
use App\Http\Requests\ApiRequest;
use App\Models\Players;
use App\Models\Statistics;
use App\Models\StatisticsHistory;
use App\Services\ApiLog;
use App\Services\GameServer;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;

class PlayerController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $players = Players::all();

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showOnlineAmount(ApiRequest $request)
    {
        //type 1 当前在线人数
        //type 2 当前最高人数
        //type 3 游戏中的人数
        try {
            $onlineAmount = Statistics::where('type', 1)->firstOrFail()->value;    //当日在线

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $onlineAmount,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showOnlinePeak(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            if (Carbon::parse($request->date)->isToday()) {
                $onlinePeak = Statistics::where('type', 2)->first();
            } else {
                $onlinePeak = StatisticsHistory::where('type', 2)
                    ->whereDate('time', $request->date)
                    ->first();
            }

            $onlinePeak = empty($onlinePeak) ? 0 : $onlinePeak->value;

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $onlinePeak,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    //查询游戏中的玩家数量
    public function showInGameCount(ApiRequest $request)
    {
        //type 1 当前在线人数
        //type 2 当前最高人数
        //type 3 游戏中的人数
        try {
            $inGameCount = Statistics::where('type', 3)->firstOrFail()->value;

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $inGameCount,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function showInGamePeak(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d'
        ]);

        try {
            if (Carbon::parse($request->date)->isToday()) {
                $inGamePeak = Statistics::where('type', 4)->first();
            } else {
                $inGamePeak = StatisticsHistory::where('type', 4)
                    ->whereDate('time', $request->date)
                    ->first();
            }

            $inGamePeak = empty($inGamePeak) ? 0 : $inGamePeak->value;

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $inGamePeak,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function search(ApiRequest $request)
    {
        //$this->validateSearchRequest($request);

        try {
            $players = Players::when($request->has('uid'), function ($query) use ($request) {
                return $query->where('id', 'like', "%{$request->uid}%");
            })->when($request->has('nickname'), function ($query) use ($request) {
                return $query->where('nickname', 'like', "%{$request->nickname}%", 'or');
            })->get();

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $players,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

//    protected function validateSearchRequest($request)
//    {
//        $this->validate($request, [
//            'uid' => 'integer',
//            'nickname' => 'string|max:255',
//        ]);
//    }

    public function topUp(ApiRequest $request)
    {
        $formData = $this->validateTopUpRequest($request);

        try {
            $topUpApi = config('custom.game_server_api_topUp');

            $gameServer = new GameServer();
            $gameServer->request('POST', $topUpApi, $formData);
            ApiLog::add($request);
            return [
                'result' => true,
                'data' => [
                    'message' => '充值成功',
                ],
            ];
        } catch (Exception $e) {
            throw new ApiException($e->getMessage());
        }
    }

    protected function validateTopUpRequest($request)
    {
        $this->validate($request, [
            'uid' => 'required|integer',
            'item_type' => 'required|integer',
            'amount' => 'required|integer',
        ]);

        return [
            'uid' => $request->uid,                     //玩家ID
            'ctype' => $request->item_type,             //充值类型
            'amount' => $request->amount,               //充值数量
            'timestamp' => Carbon::now()->timestamp,    //时间戳
        ];
    }
}
