<?php

namespace App\Config;

use Framework\Exceptions\BaseException;
use Framework\Response;

class Exception
{
    /**
     * Exception handler for Framework Exception
     *
     * @param BaseException $e
     * @return void
     */
    public static function handle(BaseException $e)
    {
        return Response::exception(['msg' => $e->getMessage()]);
    }
}