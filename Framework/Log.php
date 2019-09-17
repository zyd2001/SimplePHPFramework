<?php

namespace Framework;

use Cascade\Cascade;
use Monolog\Logger;

/**
 * class Log
 * provide simple wrapper for monolog, see monolog's doc for usage
 * url: https://github.com/Seldaek/monolog/blob/HEAD/doc/01-usage.md#configuring-a-logger
 */
class Log
{
    private static $default;

    private static function defaultLogger() : Logger
    {
        if (!isset(self::$default))
            self::$default = Cascade::getLogger($_ENV['LOGGER_DEFAULT']);
        return self::$default;
    }

    /**
     * get the logger defined in App\Config\Logger by $name
     *
     * @param string $name
     * @return Logger
     */
    public static function logger(string $name) : Logger
    {
        return Cascade::getLogger($name);
    }
    
    public static function debug(string $msg, array $context = [])
    {
        self::defaultLogger()->debug($msg, $context);
    }

    public static function info(string $msg, array $context = [])
    {
        self::defaultLogger()->info($msg, $context);
    }

    public static function notice(string $msg, array $context = [])
    {
        self::defaultLogger()->notice($msg, $context);
    }

    public static function warning(string $msg, array $context = [])
    {
        self::defaultLogger()->warning($msg, $context);
    }

    public static function error(string $msg, array $context = [])
    {
        self::defaultLogger()->error($msg, $context);
    }

    public static function critical(string $msg, array $context = [])
    {
        self::defaultLogger()->critical($msg, $context);
    }

    public static function alert(string $msg, array $context = [])
    {
        self::defaultLogger()->alert($msg, $context);
    }

    public static function emergency(string $msg, array $context = [])
    {
        self::defaultLogger()->emergency($msg, $context);
    }
}