<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\LogCurrencyOperation;
use Illuminate\Http\Request;
use App\Services\ApiLog;

class CurrencyConsumedController extends Controller
{
    //获取道具消耗记录（此记录包括后台的充值和房卡的消耗）
    public function getCurrencyLog(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            'item_type' => 'integer', //道具类型
            'game_kind' => 'integer', //游戏类型
        ]);

        ApiLog::add($request);

        $data = LogCurrencyOperation::when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate('time', $request->input('date'));
        })->when($request->has('item_type'), function ($query) use ($request) {
            return $query->where('type', $request->input('item_type'));
        })->when($request->has('game_kind'), function ($query) use ($request) {
            return $query->where('kind', $request->input('game_kind'));
        })->latest('id')->get();

        return [
            'result' => true,
            'data' => $data,
        ];
    }
}
