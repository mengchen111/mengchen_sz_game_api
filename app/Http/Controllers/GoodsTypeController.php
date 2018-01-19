<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ApiRequest;
use App\Models\GoodsType;
use Exception;
use App\Exceptions\ApiException;
use App\Services\GameServerNew;

class GoodsTypeController extends Controller
{
    public function showGoodsType(ApiRequest $request)
    {
        try {
            $goodsTypes = GoodsType::all();

            return [
                'result' => true,
                'data' => $goodsTypes,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function addGoodsType(ApiRequest $request)
    {
        try {
            $goodsId = $this->getNewestGoodsId();
            $params = $request->only([
                'goods_name',
            ]);
            $params['goods_id'] = $goodsId;
            $result = GameServerNew::request('item', 'modify_type', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    protected function getNewestGoodsId()
    {
        $lastGoodsId = GoodsType::orderBy('goods_id', 'desc')->first();
        if (empty($lastGoodsId)) {
            return 1;
        } else {
            return $lastGoodsId->goods_id + 1;
        }
    }

    public function updateGoodsType(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'goods_id', 'goods_name',
            ]);
            $result = GameServerNew::request('item', 'modify_type', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    public function deleteGoodsType(ApiRequest $request)
    {
        try {
            $params = $request->only([
                'goods_id',
            ]);
            $result = GameServerNew::request('item', 'remove_type', $params);

            return [
                'result' => true,
                'data' => $result,
            ];
        } catch (Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
