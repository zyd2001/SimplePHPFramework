<?php

namespace App\Config;

class Database
{
    /**
     * return array for setup Medoo connection with database
     * url: https://medoo.in/api/new
     *
     * @return array
     */
    public static function config() : array
    {
        if (env("DB_TYPE", true) === "sqlite")
            return [
                'database_type' => env("DB_TYPE", true),
                'database_file' => env("DB_PATH", true),
            ];
        else
            return [
                'database_type' => env("DB_TYPE", true),
                'database_name' => env("DB_NAME", true),
                'server' => env("DB_SERVER", true),
                'username' => env("DB_USERNAME", true),
                'password' => env("DB_PASSWORD", true),
                'port' => env("DB_PORT", true)
            ];
    }
}