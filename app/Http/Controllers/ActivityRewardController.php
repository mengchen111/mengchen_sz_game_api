<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Models\ActivityReward;
use Exception;

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
}
