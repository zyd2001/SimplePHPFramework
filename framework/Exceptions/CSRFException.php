<?php

namespace Framework\Exceptions;

use Framework\Response;

class CSRFException extends BaseException
{
    public function response() : Response
    {
        return parent::response()->status(403);
    }
}