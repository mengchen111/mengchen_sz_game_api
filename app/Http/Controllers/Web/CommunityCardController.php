<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\ApiException;
use App\Models\Web\CommunityCardConsumptionLog;
use App\Models\Web\CommunityList;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CommunityCardController extends Controller
{
    public function consumeCard(Request $request, CommunityList $community)
    {
        $this->validate($request, [
            'player_id' => 'required|integer|exists:account,id',
            'operation' => 'required|integer|in:0,1,2,3',   //0-冻结,1-消耗冻结,2-退还冻结,3-直接耗卡,
            'count' => 'required|integer',
            'remark' => 'string|max:255',
        ]);
        $params = $request->only(['player_id', 'operation', 'count', 'remark']);
        $operation = (int)$params['operation'];

        if ($operation === 0) {
            $this->doFrozenCard($community, $params);
        } elseif ($operation === 1) {
            $this->doConsumeFrozenCard($community, $params);
        } elseif ($operation === 2) {
            $this->doRefundFrozenCard($community, $params);
        } else {
            $this->doConsumeCard($community, $params);
        }

        return [
            'code' => -1,
            'data' => '操作成功',
        ];
    }

    protected function doFrozenCard($community, $params)
    {
        DB::transaction(function () use ($community, $params) {
            //记录耗卡日志
            $params['community_id'] = $community->id;
            CommunityCardConsumptionLog::create($params);

            //冻结community的card库存
            if ($community->card_stock < $params['count']) {
                throw new ApiException('此牌艺馆房卡库存不足');
            }
            $community->card_stock -= $params['count'];
            $community->card_frozen += $params['count'];
            $community->save();
        });
    }

    protected function doConsumeFrozenCard($community, $params)
    {
        DB::transaction(function () use ($community, $params) {
            $params['community_id'] = $community->id;
            CommunityCardConsumptionLog::create($params);

            if ($community->card_frozen < $params['count']) {
                throw new ApiException('此牌艺馆冻结的房卡不足');
            }
            $community->card_frozen -= $params['count'];
            $community->save();
        });
    }

    protected function doRefundFrozenCard($community, $params)
    {
        DB::transaction(function () use ($community, $params) {
            $params['community_id'] = $community->id;
            CommunityCardConsumptionLog::create($params);

            if ($community->card_frozen < $params['count']) {
                throw new ApiException('此牌艺馆冻结的房卡不足');
            }
            $community->card_frozen -= $params['count'];
            $community->card_stock += $params['count'];
            $community->save();
        });
    }

    protected function doConsumeCard($community, $params)
    {
        DB::transaction(function () use ($community, $params) {
            $params['community_id'] = $community->id;
            CommunityCardConsumptionLog::create($params);

            if ($community->card_stock < $params['count']) {
                throw new ApiException('此牌艺馆房卡库存不足');
            }
            $community->card_stock -= $params['count'];
            $community->save();
        });
    }
}
