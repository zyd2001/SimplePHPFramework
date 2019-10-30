<?php

namespace Framework;

use App\Config\Logger;
use Cascade\Cascade;
use Framework\Exceptions\BaseException;
use Medoo\Medoo;
use Symfony\Component\Dotenv\Dotenv;

class Initializer
{
    public static function init()
    {
        Cascade::fileConfig(Logger::config());

        $dotenv = new Dotenv(false);
        $dotenv->load("../.env");

        require_once __DIR__ . '/Helper.php';
        
        $_ENV["DEBUG"] = strtolower(env('DEBUG', true)) === "true";
        $_ENV['FASTROUTE_CACHE'] = strtolower(env('FASTROUTE_CACHE', true)) === 'true';
        $_ENV['TWIG_CACHE'] = strtolower(env('TWIG_CACHE', true)) === 'true';
            
        if ($_ENV["DEBUG"])
        {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        self::loadDirectory('../App/Routes');

        BaseException::setHandler([\App\Config\Exception::class, 'handle']);

        Router::dispatch(Request::catch())->send();
    }

    public static function setupDB(&$db)
    {
        $db = new Medoo(\App\Config\Database::config());
        $db = new Database($db);
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