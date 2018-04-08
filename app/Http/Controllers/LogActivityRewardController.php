<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use Illuminate\Http\Request;
use App\Models\LogActivityReward;
use Exception;
use App\Exceptions\ApiException;

class LogActivityRewardController extends Controller
{
    public function show(Request $request)
    {
        try {
            $result = LogActivityReward::with(['activityReward', 'player'])
                ->when($request->has('uid'), function ($query) use ($request) {
                    return $query->where('uid', $request->input('uid'));
                })
                ->get();

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
