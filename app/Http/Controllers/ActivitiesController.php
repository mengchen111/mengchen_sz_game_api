<?php

namespace App\Http\Controllers;

use App\Exceptions\ApiException;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\ApiRequest;
use App\Models\Activity;
use App\Models\ActivityReward;
use App\Services\ApiLog;
use Illuminate\Http\Request;
use Exception;

class ActivitiesController extends Controller
{
    public function showActivities(ApiRequest $request)
    {
        try {
            $activities = Activity::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $activities,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }

    }

    public function showActivitiesReward(AdminRequest $request)
    {
        try {
            $activities = ActivityReward::all();

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => $activities,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }

    }
}
