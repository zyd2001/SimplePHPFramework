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
        if ($_ENV["DB_TYPE"] === "sqlite")
            return [
                'database_type' => $_ENV["DB_TYPE"],
                'database_file' => $_ENV["DB_PATH"],
            ];
        else
            return [
                'database_type' => $_ENV["DB_TYPE"],
                'database_name' => $_ENV["DB_NAME"],
                'server' => $_ENV["DB_SERVER"],
                'username' => $_ENV["DB_USERNAME"],
                'password' => $_ENV["DB_PASSWORD"],
                'port' => $_ENV["DB_PORT"]
            ];
    }
}