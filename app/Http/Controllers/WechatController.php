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
        Log::info('wechat callback - method:' . $request->getMethod() . ' content: ' . $request->getContent());
        $this->wechatServer->setMessageHandler(function($message){
            // 注意，这里的 $message 不仅仅是用户发来的消息，也可能是事件
            // 当 $message->MsgType 为 event 时为事件
            if ($message->MsgType == 'event') {
                switch ($message->Event) {
                    case 'subscribe':   //关注公众号事件
                        $this->handleSubscribeEvent($message);
                        break;
                    case 'unsubscribe': //取消关注
                        Log:info('wechat unscribe - user: ' . $message->FromUserName);
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
        Log::info('wechat message ' . json_encode(get_object_vars($message)));

        $openId = $message->FromUserName;
        $subcriber = UnionidOpenid::where('openid', $openId)->first();
        if (!empty($subcriber)) {   //如果不为空说明已经关注了公众号，数据库有保存用户的数据
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
        return $res['openid'];
    }
}
