<?php

use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;

return [
    'application' => [
        'baseDir' => getenv('BASE_DIR'),
        'baseUri' => getenv('BASE_URI'),
        'debug'   => true,
    ],
    'modules' => [
    ],
    'profiling' => [
        'enabled' => true,
        'logger' => [
            'enabled' => true,
            'path'    => getenv('BASE_DIR') . 'var/logs/',
            'format'  => '[%date%][%type%] %message%',
            'date'    => 'Y-m-d H:i:s O'
        ]
    ],
    'error' => [
        'logger'     => new FileLogger(getenv('BASE_DIR') . 'var/logs/' . APPLICATION_ENV . '.error.log'),
        'formatter'  => new FormatterLine('[%date%][%type%] %message%', 'Y-m-d H:i:s O'),
        'controller' => 'error',
        'action'     => 'index'
    ]
];
