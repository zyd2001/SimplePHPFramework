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
        $dotenv->load("../.env");
        if ($_ENV["DEBUG"])
            $_ENV["DEBUG"] = strtolower($_ENV["DEBUG"]) === "true";
            
        if ($_ENV["DEBUG"])
        {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        self::loadDirectory('../App/Routes');
        require_once __DIR__ . '/Helper.php';

        $twig = new Environment(new FilesystemLoader("../App/Views"), ["debug" => true]);
        self::twigRegister($twig);
        Viewer::init($twig);

        Router::dispatch(Request::catch())->send();
    }

    private static function twigRegister(Environment $twig)
    {
        $twig->addFunction(new TwigFunction("csrf_token", 'csrf_token'));
        $twig->addFunction(new TwigFunction("session", 'session'));
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

    private static function loadDirectory($dir)
    {
        foreach (scandir($dir) as $file)
        {
            if (is_file($dir . '/' . $file))
                require_once $dir . '/' . $file;
        }
    }
}