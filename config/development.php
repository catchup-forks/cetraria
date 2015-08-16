<?php

$errorLogger =  new \Phalcon\Logger\Adapter\File(getenv('BASE_DIR') . 'var/logs/' . APPLICATION_ENV . '.error.log');
$errorLogger->setFormatter(new \Phalcon\Logger\Formatter\Line('[%date%][%type%] %message%', 'Y-m-d H:i:s O'));

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
        'logger'     => $errorLogger,
        'controller' => 'error',
        'action'     => 'index'
    ]
];
