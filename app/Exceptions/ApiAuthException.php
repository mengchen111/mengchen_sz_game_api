<?php
/**
 * Created by PhpStorm.
 * User: liudian
 * Date: 10/17/17
 * Time: 15:58
 */

namespace App\Exceptions;

use Exception;

class ApiAuthException extends Exception
{
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}