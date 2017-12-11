<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 10/18/17
 * Time: 10:28
 */

namespace App\Exceptions;

use Exception;

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