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
        if ($_ENV["DEBUG"])
            $_ENV["DEBUG"] = strtolower($_ENV["DEBUG"]) === "true";
        $_ENV['FASTROUTE_CACHE'] = strtolower($_ENV['FASTROUTE_CACHE']) === 'true';
        $_ENV['TWIG_CACHE'] = strtolower($_ENV['TWIG_CACHE']) === 'true';
            
        if ($_ENV["DEBUG"])
        {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        self::loadDirectory('../App/Routes');
        require_once __DIR__ . '/Helper.php';

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