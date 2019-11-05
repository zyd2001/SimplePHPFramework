<?php

namespace App\Config;

use Exception as GlobalException;
use Framework\Exceptions\CSRFException;
use Framework\Exceptions\FrameworkException;
use Framework\Exceptions\RouterException;
use Framework\Response;

class Exception
{
    /**
     * Exception handler for Framework Exception
     * before the exception form a response
     *
     * @param FrameworkException $e
     * @return Response
     */
    public static function handle(FrameworkException $e)
    {
        switch (get_class($e)) {
            case CSRFException::class:
                return Response::exception(['msg' => $e->getMessage()])->status(403);
                break;
            case RouterException::class:
                switch ($e->code) 
                {
                    case 0: 
                    {
                        return Response::notFound();
                        break;
                    }
                    case 1:
                    {
                        return Response::notFound(["msg" => $e->getMessage()])->status(405);
                    }
                    default:
                        return parent::response();
                        break;
                }
                break;
            default:
                return Response::exception(['msg' => $e->getMessage()]);
                break;
        }
    }
    
    /**
     * Exception handler for PHP \Exception
     *
     * @param \Exception $e
     * @return Response
     */
    public static function handleException(GlobalException $e) : Response
    {
        return Response::exception(['msg' => $e->getMessage()]);
    }
}