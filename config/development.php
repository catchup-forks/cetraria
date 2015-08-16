<?php

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
];
