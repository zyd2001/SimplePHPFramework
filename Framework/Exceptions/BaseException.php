<?php

namespace Framework\Exceptions;

use Framework\Response;

class BaseException extends \Exception
{
    private static $handler;

    public static function setHandler(callable $c)
    {
        self::$handler = $c;
    }

    public function response() : Response
    {
        return call_user_func(self::$handler, $this);
    }
}