<?php

namespace App\Http\Controllers;

use App\Models\LogCurrencyOperation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ApiException;
use App\Services\ApiLog;

class CardConsumedController extends Controller
{
    /**
     * 根据日期获取当日耗卡数据
     *
     * @param string $date
     * @return string '280|13|22 - 当日玩家耗卡总数|当日有过耗卡记录的玩家总数|平均耗卡数(向上取整的比值)
     */
    public function getCardConsumedData(Request $request)
    {
        $this->validateInputDate($request);

        try {
            $date = $request->input('date');
            $cardConsumedSum = $this->getCardConsumedSum($date);
            $cardConsumedPlayersCount = $this->getCardConsumedPlayersCount($date);

            if (empty($cardConsumedPlayersCount)) {
                $cardConsumedAvg = 0;
            } else {
                $cardConsumedAvg = ceil($cardConsumedSum / $cardConsumedPlayersCount);  //向上取整
            }

            ApiLog::add($request);

            return [
                'result' => true,
                'data' => "${cardConsumedSum}|${cardConsumedPlayersCount}|${cardConsumedAvg}",
            ];
        } catch (\Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }

    protected function validateInputDate(Request $request)
    {
        $this->validate($request, [
            'date' => 'required|date_format:Y-m-d'
        ]);
    }

    protected function getCardConsumedSum($date)
    {
        $data = $this->fetchData($date);
        return abs($data->sum('val'));
    }

    protected function getCardConsumedPlayersCount($date)
    {
        $data = $this->fetchData($date);
        return $data->groupBy('uid')->count();
    }

    protected function fetchData($date)
    {
        return LogCurrencyOperation::whereDate('time', $date)
            ->where('val', '<', 0)
            ->where('kind', '!=', 0)
            ->get();
    }

    /**
     * 获取截止给定日期玩家消耗卡的总数，如果不给定日期则查询截止当前玩家耗卡的总数
     **/
    public function getCardConsumedSumTotal(Request $request)
    {
        $this->validateInputDate($request);

        try {
            $sum = LogCurrencyOperation::whereDate('time', '<=', $request->input('date'))
                ->where('val', '<', 0)
                ->where('kind', '!=', 0)
                ->sum('val');

            ApiLog::add($request);
            
            return [
                'result' => true,
                'data' => abs($sum),
            ];
        } catch (\Exception $exception) {
            throw new ApiException($exception->getMessage());
        }
    }
}
