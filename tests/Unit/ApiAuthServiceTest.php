<?php

namespace Tests\Unit;

use App\Exceptions\ApiAuthException;
use App\Services\ApiAuthService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Faker\Factory as Faker;

class ApiAuthServiceTest extends TestCase
{
    /**
     * @expectedException \App\Exceptions\ApiAuthException
     */
    public function testGetSecretWithWrongKey()
    {
        ApiAuthService::getSecret(Faker::create()->name);
    }

    public function testGetSecretWithRightKey()
    {
        $key = config('custom.api_key');
        $res = ApiAuthService::getSecret($key);
        $this->assertEquals(config('custom.api_secret'), $res);
    }

    public function testBuildSign()
    {
        $params = [
            'api_key' => '111',
            'test' => 'test',
        ];
        $secret = 'secret';

        $sign = $this->buildSignRules($params, $secret);
        $testSign = ApiAuthService::buildSign($params, $secret);
        $this->assertEquals($sign, $testSign);
    }

    protected function buildSignRules($params, $secret)
    {
        ksort($params);     //将参数按照字母表升序排序
        $sign = "";
        foreach ($params as $key => $value) {   //构建签名字符串
            $sign .= "{$key}={$value}&";
        }
        $sign .= "api_secret=${secret}";        //将api_secret参数append到签名字符串的末尾
        return strtoupper(md5($sign));          //将签名字符串使用md5转码，转码完成再将其字母转为大写
    }

    public function testAuthWithRightSecret()
    {
        $apiSecret = config('custom.api_secret');
        $params = [
            'api_key' => config('custom.api_key'),
            'test' => 'test',
        ];
        $sign = $this->buildSignRules($params, $apiSecret);

        $params['sign'] = $sign;

        $res = ApiAuthService::auth($params);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \App\Exceptions\ApiAuthException
     */
    public function testAuthWithWrongSecret()
    {
        $apiSecret = Faker::create()->name;
        $params = [
            'api_key' => config('custom.api_key'),
            'test' => 'test',
        ];
        $sign = $this->buildSignRules($params, $apiSecret);

        $params['sign'] = $sign;

        ApiAuthService::auth($params);
    }
}
