<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\Marquee;
use App\Services\GameServerNew;
use Illuminate\Http\Request;
use Exception;

class MarqueeController extends Controller
{
    protected $marquee;
    protected $perPage = 15;

    public function __construct(Marquee $marquee)
    {
        parent::__construct(\request());
        $this->marquee = $marquee;
    }

    public function index(ApiRequest $request)
    {
        $marquees = $this->marquee->latest('id')->paginate($this->perPage);

        return [
            'result' => 'true',
            'data' => $marquees,
        ];
    }

    public function store(Request $request)
    {
        $this->validator($request);

        $marquee = $this->marquee->create($request->all());
        $notify = false;
        if ($marquee) {
            //通知游戏
            try {
                $notify = GameServerNew::request('marquee', 'returnid', ['id', $marquee->id]);
                $notify = $notify ? true : false;
            } catch (Exception $e) {
                $notify = $e->getMessage();
            }
        }

        return [
            'result' => 'true',
            'data' => [
                'notify_game' => $notify,
                'result' => $marquee,
            ],
        ];
    }

    public function update(ApiRequest $request, $id)
    {
        $this->validator($request);

        $notify = false;

        $marquee = $this->marquee->findOrFail($id);
        $result = $marquee->update($request->all());
        if ($result) {
            //通知游戏
            try {
                $notify = GameServerNew::request('marquee', 'returnid', ['id', $id]);
                $notify = $notify ? true : false;
            } catch (Exception $e) {
                $notify = $e->getMessage();
            }
        }

        return [
            'result' => 'true',
            'data' => [
                'notify_game' => $notify,
                'result' => $marquee,
            ],
        ];
    }

    public function destroy(ApiRequest $request, $id)
    {
        $notify = false;

        $marquee = $this->marquee->findOrFail($id);
        $result = $marquee->delete();
        if ($result) {
            //通知游戏
            try {
                $notify = GameServerNew::request('marquee', 'returnid', ['id', $id]);
                $notify = $notify ? true : false;
            } catch (Exception $e) {
                $notify = $e->getMessage();
            }
        }

        return [
            'result' => 'true',
            'data' => [
                'notify_game' => $notify,
                'result' => '删除' . ($result ? '成功' : '失败'),
            ],
        ];
    }

    public function validator($request)
    {
        $this->validate($request, [
            'level' => 'required|integer',
            'content' => 'required|string',
            'stime' => 'required|date_format:"Y-m-d H:i:s"',
            'etime' => 'required|date_format:"Y-m-d H:i:s"',
            'diff_time' => 'required|integer',
            'status' => 'required|integer',
            'sync' => 'required|integer',
        ]);
    }
}
