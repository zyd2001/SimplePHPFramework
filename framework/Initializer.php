<?php

namespace Framework;

use Symfony\Component\Dotenv\Dotenv;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class Initializer
{
    public static function init()
    {
        $dotenv = new Dotenv();
        $dotenv->load(".env");
        if ($_ENV["DEBUG"])
            $_ENV["DEBUG"] = strtolower($_ENV["DEBUG"]) === "true";
        
        self::loadDirectory('./app/Routes');
        require_once './framework/Helper.php';

        $twig = new Environment(new FilesystemLoader($_ENV["VIEWS_PATH"]), ["debug" => true]);
        $twig->addFunction(new TwigFunction("csrf_token", 'csrf_token'));
        $twig->addFunction(new TwigFunction("csrf_field", function () {
            return '<input type="hidden" name="csrf_token" value=' . csrf_token() . '>';
        }, ['is_safe' => ['html']]));
        Viewer::init($twig);

        if ($_ENV["DEBUG"])
        {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        Router::dispatch(Request::catch())->send();
    }

    private static function loadDirectory($dir)
    {
        foreach (scandir($dir) as $file)
        {
            if (is_file($dir . '/' . $file))
                require_once $dir . '/' . $file;
        }
    }
}