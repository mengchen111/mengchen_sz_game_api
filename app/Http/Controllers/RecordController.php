<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\Players;
use App\Models\RecordInfos;
use App\Models\RecordInfosNew;
use App\Models\RecordRelative;
use App\Models\RecordRelativeNew;
use App\Models\ServerRoomsHistory;
use App\Models\V1\Record;
use App\Models\V1\RoomsHistory;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\ApiException;
use App\Services\ApiLog;

class RecordController extends Controller
{
    //暂未使用
//    public function show(ApiRequest $request)
//    {
//        try {
//            //$records = Players::with(['records.infos'])->get();   //records关系已更改
//
//            ApiLog::add($request);
//            return [
//                'result' => true,
//                'data' => $records,
//            ];
//        } catch (Exception $exception) {
//            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
//        }
//    }

    //根据玩家id获取玩家所有战绩
    public function search(ApiRequest $request)
    {
        $searchUid = $this->filterRequest($request);

        try {
            $player = Players::where('id', "$searchUid")->first();

            if (empty($player)) {
                return [
                    'result' => true,
                    'data' => [],
                ];
            }

            $records = $player->getRecords();

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $records,     //战绩为空时，data为空数组
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    public function searchRoom(ApiRequest $request)
    {
        $searchRoomId = $this->filterSearchRoomRequest($request);
        try {
            $rooms = ServerRoomsHistory::query()->where('rid',$searchRoomId)->first();

            //新版
//            $rooms = RoomsHistory::query()->where('rid',$searchRoomId)->first();

            $records = $rooms->getRecords();
            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $records,     //战绩为空时，data为空数组
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    //根据战绩id查询单条战绩详情
    public function searchRecordInfo(ApiRequest $request)
    {
        $searchRecId = $this->filterSearchRecordRequest($request);

        try {
            //不再兼容老的战绩查询，两张数据库表结构不一致
//            if ($searchRecId >= 100000) {
//                $rounds = RecordInfosNew::find($searchRecId);
//            } else {
//                $rounds = RecordInfos::find($searchRecId);
//            }
            $rounds = RecordInfosNew::find($searchRecId);

            //新版
//            $rounds = Record::find($searchRecId);

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $rounds,
            ];
        } catch (\Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    protected function filterRequest($request)
    {
        $this->validate($request, [
            'uid' => 'required|integer|exists:account,id',
        ], [
            'exists' => '玩家不存在',
        ]);
        return $request->uid;
    }

    protected function filterSearchRecordRequest($request)
    {
        $this->validate($request, [
            'rec_id' => 'required|integer',
        ], [
            'exists' => '玩家不存在',
        ]);
        return $request->rec_id;
    }

    protected function filterSearchRoomRequest($request)
    {
        $this->validate($request, [
//            'rid' => 'required|integer|exists:server_rooms_history_4,rid',
        ], [
//            'exists' => '房间不存在',
        ]);
        return $request->rid;
    }
}