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
            'start_time' => 'nullable|required_with_all:end_time|date_format:"Y-m-d H:i:s"',
            'end_time' => 'nullable|required_with_all:start_time|date_format:"Y-m-d H:i:s"',
            'community_id' => 'integer',
        ]);

        $data = LogCurrencyOperation::when($request->has('date'), function ($query) use ($request) {
            return $query->whereDate('time', $request->input('date'));
        })->when($request->has('item_type'), function ($query) use ($request) {
            return $query->where('type', $request->input('item_type'));
        })->when($request->has('game_kind'), function ($query) use ($request) {
            return $query->where('kind', $request->input('game_kind'));
        })->when($request->has('start_time'), function ($query) use ($request) {
            return $query->whereBetween('time', [$request->input('start_time'), $request->input('end_time')]);
        })->when($request->has('community_id'), function ($query) use ($request) {
            return $query->where('community_id', $request->input('community_id'));
        })->latest('id')->get();

        return [
            'result' => true,
            'data' => $data,
        ];
    }
}
