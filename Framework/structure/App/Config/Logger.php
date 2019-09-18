<?php

namespace App\Config;

class Logger
{
    /**
     * return array for setup monolog-cascade
     * url: https://github.com/theorchard/monolog-cascade
     *
     * @return array
     */
    public static function config(): array
    {
        return [
            'formatters' => [
                'spaced' => [
                    'format' => "%datetime% %channel%.%level_name%  %message%  %context%\n",
                ]
            ],
            'handlers' => [
                'file_handler' => [
                    'class' => 'Monolog\Handler\StreamHandler',
                    'level' => 'DEBUG',
                    'formatter' => 'spaced',
                    'stream' => '../Log/debug.log'
                ]
            ],
            'loggers' => [
                'debug_logger' => [
                    'handlers' => ['file_handler']
                ]
            ]
        ];
    }
}
