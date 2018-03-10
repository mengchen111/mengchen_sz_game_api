<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Models\Tasks;
use App\Models\TaskType;
use Exception;
use App\Exceptions\ApiException;
use App\Services\GameServerNew;

class TasksController extends Controller
{
    public function showTask(ApiRequest $request)
    {
        try {
            $tasks = Tasks::with('typeModel')->get();

            return [
                'result' => true,
                'data' => $tasks,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function addTask(ApiRequest $request)
    {
        try {
            $taskId = $this->getNewestTaskId();
            $params = $request->only([
                'name', 'type', 'begin_time', 'end_time', 'mission_time', 'target', 'reward', 'daily', 'link',
            ]);
            $params['id'] = $taskId;
            $result = GameServerNew::request('task', 'modify_task', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    protected function getNewestTaskId()
    {
        $lastTask = Tasks::orderBy('id', 'desc')->first();
        if (empty($lastTask)) {
            return 1;
        } else {
            return $lastTask->id + 1;
        }
    }

    public function updateTask(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'id', 'name', 'type', 'begin_time', 'end_time', 'mission_time', 'target', 'reward', 'daily', 'link',
            ]);
            $result = GameServerNew::request('task', 'modify_task', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function deleteTask(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'id',
            ]);
            $result = GameServerNew::request('task', 'remove_task', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
