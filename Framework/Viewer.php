<?php

namespace Framework;

use Framework\Exceptions\FileException;

class Viewer
{
    private $file;
    private static $twig;
    private static $notFoundPage;
    private static $exceptionPage;

    /**
     * construct a new Viewer instance of $file
     *
     * @param string $file
     */
    public function __construct(string $file) 
    {
        if (is_file("../App/Views/" . $file))
            $this->file = $file;
        else
            throw new FileException("No such view: $file", 1);            
    }

    public static function init($twig)
    {
        static::$twig = $twig;
    }

    /**
     * render a response with $arr
     *
     * @param array $arr
     * @return Response
     */
    public function view(array $arr = []) : Response
    {
        return new Response(static::$twig->render($this->file, $arr));
    }

    /**
     * return not found page viewer
     *
     * @return Viewer
     */
    public static function notFoundPage() : Viewer
    {
        if (static::$notFoundPage === null)
        {
            static::$notFoundPage = new Viewer("404.html");
            return static::$notFoundPage;
        }
        else
            return static::$notFoundPage;
    }

    /**
     * return exception page viewer
     *
     * @return Viewer
     */
    public static function exceptionPage() : Viewer
    {
        if (static::$exceptionPage === null)
        {
            static::$exceptionPage = new Viewer("exception.html");
            return static::$exceptionPage;
        }
        else
            return static::$exceptionPage;
    }
}
