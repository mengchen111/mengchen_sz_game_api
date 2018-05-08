<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\ApiRequest;
use App\Traits\ApiRequestBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommunityGameRecordController extends Controller
{
    use ApiRequestBuilder;

    public function search(Request $request, $communityId)
    {
        $this->validate($request, [
            'start_time' => 'required|date_format:"Y-m-d H:i:s"',
            'end_time' => 'required|date_format:"Y-m-d H:i:s"',
            //'community_id' => 'required|integer',
            'player_id' => 'integer'
        ]);
        $params = $request->intersect(['start_time', 'end_time', 'player_id']);
        $params['community_id'] = $communityId;

        $records = $this->getRecords($params);

        $result = $this->formatRecords($records);

        return $this->res($result);
    }

    protected function getRecords($params)
    {
        $controller = app()->make('App\Http\Controllers\RoomController');
        $params = $this->buildParams($params);
        $request = new ApiRequest();
        $request->setMethod('POST');
        $request->request->add($params);
        $result = $controller->searchCommunityRoomRecord($request)['data'];
        $result['records'] = $result['records']->toArray();
        return $result;
    }

    protected function formatRecords($records)
    {
        $count = 0;
        foreach ($records['records'] as &$record) {
            unset($record['options_jstr']); //不显示玩法详情
            if (!empty($record['record_info'])) {
                unset($record['record_info']['rec_jstr']);  //不现实战绩详情
                if ((int)$record['record_info']['if_read'] === 0) {
                    $count += 1;
                }
            }
            //大赢家（得分最高者）标识
            array_reduce([2,3,4], function (Array $v1, $v2) use (&$record) {
                //$record['player' . $v2]['is_winner'] = true;    //先将第一个玩家设置为赢家
                if ($record['score_' . $v1[0]] < $record['score_' . $v2]) {
                    //只有当第二个玩家的分数大于第一个玩家的时候才将第二个玩家设置为大赢家
                    $record['player' . $v2]['is_winner'] = true;
                    //将第一个玩家赢家标识取消(可能存在多个大赢家)
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = false;
                    });
                    return [$v2];
                } elseif ($record['score_' . $v1[0]] === $record['score_' . $v2]) {
                    $record['player' . $v2]['is_winner'] = true;
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = true;
                    });
                    array_push($v1, $v2);
                    return $v1;
                } else {
                    $record['player' . $v2]['is_winner'] = false;
                    array_walk($v1, function ($value) use (&$record) {
                        $record['player' . $value]['is_winner'] = true;
                    });
                    return $v1;
                }
            }, [1]);
        }
        $result['unread_records'] = $count;
        $result['records'] = $records['records'];
        $result['total_unread_record_cnt'] = $records['total_unread_record_cnt'];
        return $result;
    }

    public function markRecord(Request $request, $recordInfoId)
    {
        $api = config('custom.game_api_community_record_mark');
        $params['record_info_id'] = $recordInfoId;
        $params['if_read'] = 1;
        GameApiService::request('POST', $api, $params);

        return [
            'message' => '查看成功',
        ];
    }
}
