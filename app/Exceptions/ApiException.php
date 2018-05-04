<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 10/18/17
 * Time: 10:28
 */

namespace App\Exceptions;

use Exception;

/**
 * @SWG\Definition(
 *     definition="ApiError",
 *     type="object",
 *     @SWG\Property(
 *         property="result",
 *         description="结果(false)",
 *         type="boolean",
 *         default="false",
 *         example="false",
 *     ),
 *     @SWG\Property(
 *         property="code",
 *         description="返回码，大于等于0",
 *         type="integer",
 *         format="int32",
 *         default=2000,
 *         example=2000,
 *     ),
 *     @SWG\Property(
 *         property="errorMsg",
 *         description="错误消息提示",
 *         type="string",
 *         example="原密码错误",
 *     ),
 * ),
 */
class ApiException extends Exception
{
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        if (empty($code)) {
            $code = config('exceptions.ApiException', 0);
        }
        parent::__construct($message, $code, $previous);
    }
}