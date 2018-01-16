<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\LogRedbag;
use App\Services\ApiLog;
use Illuminate\Http\Request;

class WechatRedPacketController extends Controller
{
    public function getSendList(ApiRequest $request)
    {
        $this->validate($request, [
            'sent' => 'integer|in:0,1,2',
        ]);

        ApiLog::add($request);

        $sent = $request->input('sent');
        $sendList = LogRedbag::with(['activityReward', 'player'])
            ->when($request->has('sent'), function ($query) use ($sent) {
                return $query->where('sent', $sent);
            })
            ->get();

        return [
            'result' => true,
            'data' => $sendList,
        ];
    }

    public function updateSendStatus(ApiRequest $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'sent' => 'required|integer|in:0,1,2',
            'sent_time' => 'date_format:"Y-m-d H:i:s',
            'error' => 'string',
        ]);
        $data = $request->only(['id', 'sent', 'sent_time', 'error']);

        ApiLog::add($request);

        LogRedbag::where('id', $data['id'])->update($data);

        return [
            'result' => true,
            'data' => '更新成功',
        ];
    }
}
