<?php

namespace App\Services;

use GuzzleHttp;
use App\Exceptions\GameServerException;
use BadMethodCallException;
use Carbon\Carbon;

class GameServerNew
{
    public static function __callStatic($name, $arguments)
    {
        switch ($name) {
            case 'apiAddress':
                return config('custom.game_server_api_address_new');
                break;
            case 'partnerId':
                return config('custom.game_server_partner_id_new');
                break;
            default:
                throw new BadMethodCallException('Call to undefined method ' . self::class . "::${name}()");
        }
    }

    protected static function httpClient()
    {
        return new GuzzleHttp\Client([
            'connect_timeout' => 5,
        ]);
    }

    protected static function buildSign(Array $params)
    {
        ksort($params);

        $sign = '';
        array_walk($params, function ($v, $k) use (&$sign) {
            $sign .= "{$k}={$v}&";
        });
        $sign .= 'key=' . self::partnerId();
        $sign = strtoupper(md5($sign));

        return $sign;
    }

    protected static function buildParams($P, $F, $params)
    {
        $params['P'] = $P;
        $params['F'] = $F;
        $params['timestamp'] = Carbon::now()->timestamp;
        return $params;
    }

    //$P：接口名，$F：操作方法，$params：参数
    public static function request($P, $F, Array $params = [])
    {
        $gameServerApi = self::apiAddress();
        $params = self::buildParams($P, $F, $params);
        $params['sign'] = self::buildSign($params);

        try {
            $res = self::httpClient()->post($gameServerApi, [
                'form_params' => $params,
            ])
                ->getBody()
                ->getContents();
        } catch (\Exception $exception) {
            throw new GameServerException('调用后端接口失败：' . $exception->getMessage(), $exception);
        }

        $result = self::decodeResponse($res);

        self::checkResult($result);

        return $result;
    }

    protected static function decodeResponse($res)
    {
        return json_decode(base64_decode($res), true);
    }

    protected static function checkResult($result)
    {
        if (empty($result)) {
            throw new GameServerException('调用后端接口成功，但是返回结果为空：' . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        if ((int) $result['code'] !== 1) {
            throw new GameServerException('调用后端接口成功，但是游戏服返回的结果错误：' . json_encode($result, JSON_UNESCAPED_UNICODE));
        }
        return true;
    }
}