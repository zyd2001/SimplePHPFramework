<?php

namespace Framework;

use App\Config\Database;
use App\Config\Logger;
use Cascade\Cascade;
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
            
        if ($_ENV["DEBUG"])
        {
            error_reporting(E_ALL);
            ini_set("display_errors", 1);
        }

        self::loadDirectory('../App/Routes');
        require_once __DIR__ . '/Helper.php';

        Router::dispatch(Request::catch())->send();
    }

    public static function setupDB(&$db)
    {
        $db = new Medoo(Database::config());
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