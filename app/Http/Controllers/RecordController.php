<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\Players;
use App\Models\RecordInfos;
use App\Models\RecordRelative;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\ApiException;
use App\Services\ApiLog;

class RecordController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $records = Players::with(['records.infos'])->get();

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $records,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    //根据玩家id获取玩家所有战绩
    public function search(ApiRequest $request)
    {
        $searchUid = $this->filterRequest($request);

        try {
            $records = Players::with(['records.infos'])
                ->where('id', "$searchUid")
                ->first()
                ->records;

            ApiLog::add($request);
            return [
                'result' => true,
                'data' => $records,
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
            $rounds = RecordInfos::find($searchRecId);

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
            'uid' => 'required|numeric|exists:account,id',
        ], [
            'exists' => '玩家不存在',
        ]);
        return $request->uid;
    }

    protected function filterSearchRecordRequest($request)
    {
        $this->validate($request, [
            'rec_id' => 'required|numeric|exists:record_infos,id',
        ], [
            'exists' => '玩家不存在',
        ]);
        return $request->rec_id;
    }
}