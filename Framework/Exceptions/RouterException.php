<?php

namespace Framework\Exceptions;

use Framework\Response;

class RouterException extends BaseException
{
    public function response() : Response
    {
        switch ($this->code) {
            case 0: 
            {
                return Response::notFound();
                break;
            }
            case 1:
            {
                return Response::notFound(["msg" => $this->getMessage()])->status(405);
            }
            default:
                # code...
                break;
        }
    }
}