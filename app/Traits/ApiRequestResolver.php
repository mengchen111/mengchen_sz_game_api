<?php

namespace App\Traits;

use App\Http\Requests\ApiRequest;

trait ApiRequestResolver
{
    protected function buildParams(Array $params)
    {
        $params['api_key'] = env('API_KEY');
        $sign = self::buildSign($params);
        $params['sign'] = $sign;
        return $params;
    }

    protected static function buildSign(Array $params)
    {
        ksort($params);                             //将参数按照字母表升序排序

        //构建签名字符串
        $sign = '';
        array_walk($params, function ($v, $k) use (&$sign) {
            $sign .= "{$k}={$v}&";
        });
        //将api_secret参数append到签名字符串的末尾
        $sign .= 'api_secret=' . env('API_SECRET');
        //将签名字符串使用md5转码，转码完成再将其字母转为大写
        $sign = strtoupper(md5($sign));

        return $sign;
    }

    protected function callController($controllerFunc, $params, $method)
    {
        list($controllerName, $func) = explode('@', $controllerFunc);
        $controller = app()->make($controllerName);
        $params = $this->buildParams($params);

        $request = new ApiRequest();
        $request->setMethod($method);
        $request->merge($params);

        $result = $controller->$func($request);
        return $result['data'];
    }
}