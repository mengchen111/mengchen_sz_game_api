<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApiRequest;
use App\Models\Marquee;
use App\Services\GameServerNew;
use Illuminate\Http\Request;

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
        $marquees = $this->marquee->paginate($this->perPage);

        return [
            'result' => 'true',
            'data' => $marquees,
        ];
    }

    public function store(ApiRequest $request)
    {
        $marquee = $this->marquee->create($request->all());
        $notify = false;
        if ($marquee) {
            //通知游戏
            $notify = GameServerNew::request('marquee', 'returnid', ['id', $marquee->id]);
        }

        return [
            'result' => 'true',
            'notify_game' => $notify ? 'true' : 'false',
            'data' => $marquee,
        ];
    }

    public function update(ApiRequest $request, $id)
    {
        $notify = false;

        $marquee = $this->marquee->findOrFail($id);
        $result = $marquee->update($request->all());
        if ($result) {
            //通知游戏
            $notify = GameServerNew::request('marquee', 'returnid', ['id', $id]);
        }

        return [
            'result' => 'true',
            'notify_game' => $notify ? 'true' : 'false',
            'data' => $marquee,
        ];
    }

    public function destroy(ApiRequest $request, $id)
    {
        $notify = false;

        $marquee = $this->marquee->findOrFail($id);
        $result = $marquee->delete();
        if ($result) {
            //通知游戏
            $notify = GameServerNew::request('marquee', 'returnid', ['id', $id]);
        }

        return [
            'result' => 'true',
            'notify_game' => $notify ? 'true' : 'false',
            'data' => '删除' . ($result ? '成功' : '失败'),
        ];
    }
}
