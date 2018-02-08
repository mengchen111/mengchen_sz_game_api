<?php

namespace App\Http\Controllers;

use App\Models\UserGoods;
use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use Exception;
use App\Exceptions\ApiException;
use App\Services\GameServerNew;

class UserGoodsController extends Controller
{
    public function showUserGoods(ApiRequest $request)
    {
        try {
            $activities = UserGoods::with(['goodsTypeModel', 'player'])->get();

            return [
                'result' => true,
                'data' => $activities,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function addUserGoods(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'user_id', 'goods_id', 'goods_cnt',
            ]);

            $existUserGoods = UserGoods::where('user_id', $params['user_id'])
                ->where('goods_id', $params['goods_id'])
                ->get();

            if (!$existUserGoods->isEmpty()) {
                throw new ApiException('已存在的玩家物品');
            }

            $result = GameServerNew::request('item', 'modify_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function updateUserGoods(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'user_id', 'goods_id', 'goods_cnt',
            ]);

            $existUserGoods = UserGoods::where('user_id', $params['user_id'])
                ->where('goods_id', $params['goods_id'])
                ->get();

            if ($existUserGoods->isEmpty()) {
                throw new ApiException('不存在的玩家物品，请先添加之');
            }

            $result = GameServerNew::request('item', 'modify_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function deleteUserGoods(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'user_id', 'goods_id',
            ]);
            $result = GameServerNew::request('item', 'remove_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function resetUserGoods(Request $request)
    {
        //todo
        try {
            $params = $request->only([
                'goods_id',
            ]);
            $result = GameServerNew::request('item', 'remove_user', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
