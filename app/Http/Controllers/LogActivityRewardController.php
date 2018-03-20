<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\Models\LogActivityReward;
use Exception;
use App\Exceptions\ApiException;

class LogActivityRewardController extends Controller
{
    public function show(ApiRequest $request)
    {
        try {
            $result = LogActivityReward::with(['activityReward', 'player'])->get();

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
