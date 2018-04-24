<?php

namespace App\Http\Controllers;

use App\Models\TasksPlayer;
use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use Exception;
use App\Exceptions\ApiException;
use App\Services\GameServerNew;

class TasksPlayerController extends Controller
{
    public function showTasksPlayer(ApiRequest $request)
    {
        try {
            $activities = TasksPlayer::with(['task', 'player'])->get();

            return [
                'result' => true,
                'data' => $activities,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function addTasksPlayer(ApiRequest $request)
    {
        try {
            //$tasksPlayerId = $this->getNewestTasksPlayerId();
            $params = $request->only([
                'task_id', 'uid', 'process', 'is_completed', 'count'
            ]);
            $existTasksPlayer = TasksPlayer::where('uid', $params['uid'])
                ->where('task_id', $params['task_id'])
                ->get();

            if (! $existTasksPlayer->isEmpty()) {
                throw new ApiException('已存在的玩家任务');
            }

            //$params['id'] = $tasksPlayerId;
            $result = GameServerNew::request('task', 'modify_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

//    protected function getNewestTasksPlayerId()
//    {
//        $lastTasksPlayer = TasksPlayer::orderBy('id', 'desc')->first();
//        if (empty($lastTasksPlayer)) {
//            return 1;
//        } else {
//            return $lastTasksPlayer->id + 1;
//        }
//    }

    public function updateTasksPlayer(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'task_id', 'uid', 'process', 'is_completed', 'count'
            ]);

            $existTasksPlayer = TasksPlayer::where('uid', $params['uid'])
                ->where('task_id', $params['task_id'])
                ->get();

            if ($existTasksPlayer->isEmpty()) {
                throw new ApiException('不存在的玩家任务，请先添加之');
            }

            $result = GameServerNew::request('task', 'modify_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function deleteTasksPlayer(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'uid', 'task_id',
            ]);
            $result = GameServerNew::request('task', 'remove_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function resetTasksPlayer(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'id',
            ]);
            $result = GameServerNew::request('task', 'reset_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
