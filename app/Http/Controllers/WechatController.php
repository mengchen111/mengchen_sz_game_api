<?php

namespace App\Http\Controllers;

use App\Models\UnionidOpenid;
use App\Services\WechatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp;

class WechatController extends Controller
{
    protected $wechat;
    protected $wechatServer;
    protected $http;
    protected $wechatApi = 'https://api.weixin.qq.com/';

    public function __construct()
    {
        $this->wechat = app('wechat');
        $this->wechatServer = $this->wechat->server;
        $this->http = new GuzzleHttp\Client([
            'base_uri' => $this->wechatApi,
            'connect_timeout' => 5,
        ]);
    }

    public function callback(Request $request)
    {
        $this->wechatServer->setMessageHandler(function ($message) use ($request) {
            // 注意，这里的 $message 不仅仅是用户发来的消息，也可能是事件
            // 当 $message->MsgType 为 event 时为事件
            if ($message->MsgType == 'event') {
                Log::info('wechat method:' . $request->getMethod() . ' event: '
                    . $message->Event . ' openid:' . $message->FromUserName);

                switch ($message->Event) {
                    case 'subscribe':   //关注公众号事件
                        $this->handleSubscribeEvent($message);
                        break;
                    case 'unsubscribe': //取消关注
                        $this->handleUnsubscribeEvent($message);
                        break;
                    default:
                        # code...
                        break;
                }
            }
        });

        $response = $this->wechatServer->serve();

        //微信服务器如果
        return $response;
    }

    //如果是关注公众号的事件，就往数据库插入条目
    protected function handleSubscribeEvent($message)
    {
        $openId = $message->FromUserName;
        $record = UnionidOpenid::where('openid', $openId)->first();
        if (!empty($record)) {   //如果不为空说明已经关注了公众号，数据库有保存用户的数据
            return true;
        } else {
            $unionId = $this->getSubscriberUnionId($message->FromUserName);
            UnionidOpenid::create([
                'unionid' => $unionId,
                'openid' => $openId,
            ]);
            return true;
        }
    }

    protected function getSubscriberUnionId($openId)
    {
        $accessToken = $this->wechat->access_token;
        $token = $accessToken->getToken();
        $res = WechatService::getUnionId($token, $openId);
        return $res['unionid'];
    }

    //如果用户取消关注之后，删除数据库对应条目
    protected function handleUnsubscribeEvent($message)
    {
        $openId = $message->FromUserName;
        $record = UnionidOpenid::where('openid', $openId)->first();
        if (!empty($record)) {      //如果没有此条记录就忽略之(比如此功能没上线之前就关注了的人数据库是没有条目的)
            return true;
        } else {
            $record->delete();
            return true;
        }
    }
}
