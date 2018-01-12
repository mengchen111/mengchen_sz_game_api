<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WechatController extends Controller
{
    public function testToken(Request $request)
    {
        Log::info('wechat - ' . json_encode($request->all()));
    }
}
