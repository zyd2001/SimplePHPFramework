<?php

namespace Framework;

use Framework\Exceptions\FileException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

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

    private static function twig()
    {
        if (!isset(self::$twig))
        {
            $twig = new Environment(new FilesystemLoader("../App/Views"), ["debug" => env('DEBUG', true), 
                "cache" => env('TWIG_CACHE', true) ? "../storage/cache/twig" : false]);
            self::twigRegister($twig);
            self::$twig = $twig;
        }
        return self::$twig;
    }

    private static function twigRegister(Environment $twig)
    {
        $twig->addFunction(new TwigFunction("csrf_token",  __NAMESPACE__ . '\csrf_token'));
        $twig->addFunction(new TwigFunction("session", __NAMESPACE__ . '\session'));
        $twig->addFunction(new TwigFunction("csrf_field", function () {
            return '<input type="hidden" name="csrf_token" value=' . csrf_token() . '>';
        }, ['is_safe' => ['html']]));
        $twig->addFunction(new TwigFunction('js', function ($src) {
            return '<script type="text/javascript" src="' . js($src) . '"></script>';
        }, ['is_safe' => ['html']]));
        $twig->addFunction(new TwigFunction('css', function ($src) {
            return '<link href="' . css($src) . '" rel="stylesheet" type="text/css">';
        }, ['is_safe' => ['html']]));
    }

    /**
     * render a response with $arr
     *
     * @param array $arr
     * @return Response
     */
    public function view(array $arr = []) : Response
    {
        return new Response(static::twig()->render($this->file, $arr));
    }

    /**
     * return not found page viewer
     *
     * @return Viewer
     */
    public static function notFoundPage() : Viewer
    {
        if (!isset(self::$notFoundPage))
            static::$notFoundPage = new Viewer("404.html");
        return static::$notFoundPage;
    }

    /**
     * return exception page viewer
     *
     * @return Viewer
     */
    public static function exceptionPage() : Viewer
    {
        if (!isset(self::$exceptionPage))
            static::$exceptionPage = new Viewer("exception.html");
        return static::$exceptionPage;
    }
}
