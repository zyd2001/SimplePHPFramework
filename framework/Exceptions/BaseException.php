<?php

namespace Framework\Exceptions;

use Framework\Response;
use Framework\Viewer;

class BaseException extends \Exception
{
    public function response() : Response
    {
        return Viewer::exceptionPage()->view(["msg" => $this->getMessage()])
            ->status(500);
    }
}