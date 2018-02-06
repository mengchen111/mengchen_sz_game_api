<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\Models\UnionidOpenid;
use App\Services\ApiLog;

class WechatUnionidOpenidController extends Controller
{
    public function create(ApiRequest $request)
    {
        $this->validate($request, [
            'unionid' => 'required|string|max:64',
            'openid' => 'required|string|max:64',
        ]);

        //如果能找到unionid说明已经关注了公众号，数据库有保存用户的数据
        $record = UnionidOpenid::updateOrCreate([
            'unionid' => $request->input('unionid'),
        ], [
            'openid' => $request->input('openid')
        ]);

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => $record,
        ];
    }

    public function destroy(ApiRequest $request)
    {
        $this->validate($request, [
            'openid' => 'required|string|max:64',
        ]);

        $openId = $request->input('openid');
        $record = UnionidOpenid::where('openid', $openId)->first();

        if (! empty($record)) {      //如果没有此条记录就忽略之(比如此功能没上线之前就关注了的人数据库是没有条目的)
            $record->delete();
        }

        ApiLog::add($request);

        return [
            'result' => true,
            'data' => '删除成功',
        ];
    }
}
