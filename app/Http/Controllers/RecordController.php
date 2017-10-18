<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\Players;
use App\Models\RecordInfos;
use App\Models\RecordRelative;
use Illuminate\Http\Request;
use Exception;
use App\Exceptions\ApiException;

class RecordController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $records = Players::with(['records.infos'])->get();
            return [
                'result' => true,
                'data' => $records,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage(), config('exceptions.ApiException'));
        }
    }

    public function search(ApiRequest $request)
    {
        $searchUid = $this->filterRequest($request);

        try {
            $records = Players::with(['records.infos'])
                ->where('id', "$searchUid")
                ->first()
                ->records;
            return [
                'result' => true,
                'data' => $records,
            ];
        } catch (Exception $exception) {
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
}