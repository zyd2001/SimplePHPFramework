<?php

namespace Framework\Exceptions;

use Framework\Response;

class BaseException extends \Exception
{
    public function response() : Response
    {
        return Response::exception(['msg' => $this->getMessage()]);
    }
}