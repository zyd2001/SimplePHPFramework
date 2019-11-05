<?php

namespace Framework\Exceptions;

use Framework\Response;

class CSRFException extends FrameworkException
{
    public function response() : Response
    {
        return parent::response()->status(403);
    }
}