<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\LogCurrencyOperation;
use Illuminate\Http\Request;

class CurrencyConsumedController extends Controller
{
    //获取道具消耗记录（此记录包括后台的充值和房卡的消耗）
    public function getCurrencyLog(ApiRequest $request)
    {
        $this->validate($request, [
            'date' => 'date_format:"Y-m-d"'
        ]);

        if ($request->has('date')) {
            return LogCurrencyOperation::whereDate('time', $request->input('date'))->get();
        }
        return LogCurrencyOperation::all();
    }
}
