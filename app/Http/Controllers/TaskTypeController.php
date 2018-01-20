<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Models\TaskType;
use Exception;
use App\Exceptions\ApiException;

class TaskTypeController extends Controller
{

    public function showTaskType(ApiRequest $request)
    {
        try {
            $taskTypes = TaskType::all();

            return [
                'result' => true,
                'data' => $taskTypes,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
