<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Models\ActivityReward;
use Exception;
use App\Services\GameServerNew;
use App\Exceptions\ApiException;

class ActivityRewardController extends Controller
{
    public function showActivitiesReward(ApiRequest $request)
    {
        try {
            $rewards = ActivityReward::with('goodsTypeModel')->get();

            return [
                'result' => true,
                'data' => $rewards,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function addActivityReward(ApiRequest $request)
    {
        try {
            $pid = $this->getNewestPid();
            $params = $request->only([
                'name', 'img', 'show_text', 'total_inventory', 'probability', 'single_limit',
                'expend', 'goods_type', 'goods_count',
            ]);
            $params['pid'] = $pid;
            $result = GameServerNew::request('activity_reward', 'modify', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    protected function getNewestPid()
    {
        $lastActivityReward = ActivityReward::orderBy('pid', 'desc')->first();
        if (empty($lastActivityReward)) {
            return 1;
        } else {
            return $lastActivityReward->pid + 1;
        }
    }

    public function updateActivityReward(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'pid', 'name', 'img', 'show_text', 'total_inventory', 'probability', 'single_limit',
                'expend', 'goods_type', 'goods_count',
            ]);
            $result = GameServerNew::request('activity_reward', 'modify', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function deleteActivityReward(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'pid',
            ]);
            $result = GameServerNew::request('activity_reward', 'remove', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}