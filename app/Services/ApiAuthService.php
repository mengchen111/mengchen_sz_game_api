<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 10/17/17
 * Time: 15:28
 *
 * 验证传入的参数的key和secret是否合法
 */

namespace App\Services;

use App\Exceptions\ApiAuthException;

class ApiAuthService
{
    public static function auth(Array $params)
    {
        $apiSecret = self::getSecret($params['api_key']);
        $userSign = $params['sign'];     //用户提交的签名
        unset($params['sign']);

        $sign = self::buildSign($params, $apiSecret);

        if ($userSign !== $sign) {
            throw new ApiAuthException('sign不匹配', config('exceptions.SignNotMatch'));
        }
        return true;
    }

    public static function getSecret($key)
    {
        if ($key !== config('custom.api_key')) {
            throw new ApiAuthException('api_key不匹配', config('exceptions.ApiKeyNotMatch'));
        }
        return config('custom.api_secret');
    }

    public static function buildSign($params, $secret)
    {
        ksort($params);
        $sign = "";
        foreach ($params as $key => $value) {
            $sign .= "{$key}={$value}&";
        }
        $sign .= "secret_key=${secret}";
        return strtoupper(md5($sign));
    }
}