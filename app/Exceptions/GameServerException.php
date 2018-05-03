<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 9/26/17
 * Time: 12:23
 */

namespace App\Exceptions;

use Exception;

/**
 * @SWG\Definition(
 *     definition="GameServerError",
 *     type="object",
 *     @SWG\Property(
 *         property="result",
 *         description="结果(false)",
 *         type="boolean",
 *         default="false",
 *     ),
 *     @SWG\Property(
 *         property="code",
 *         description="返回码，大于等于0",
 *         type="integer",
 *         format="int32",
 *         default="2001",
 *     ),
 *     @SWG\Property(
 *         property="errorMsg",
 *         description="错误消息提示",
 *         type="string",
 *         example="原密码错误",
 *     ),
 * ),
 */
class GameServerException extends Exception
{
    protected $code;

    public function __construct($message = '', Exception $previous = null)
    {
        $this->code = config('exceptions.GameServerException', 0);
        parent::__construct($message, $this->code, $previous);
    }
}