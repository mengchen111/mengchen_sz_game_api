<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 10/18/17
 * Time: 09:39
 */

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Carbon\Carbon;

class ApiLog
{
    public static function add(Request $request, $message = '')
    {
        Log::info("'${message}' " . "'/" . $request->path() . "' " . "'" . $request->method() . "' "
            . "'" . $request->header('User-Agent') . "' " . "'" . json_encode($request->all()) . "'");
    }
}