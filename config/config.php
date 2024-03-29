<?php

/*
 +------------------------------------------------------------------------+
 | Cetraria                                                               |
 +------------------------------------------------------------------------+
 | Copyright (c) 2015 Serghei Iakovlev                                    |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to sadhooklay+cetraria@gmail.com so I can send you a copy immediately. |
 +------------------------------------------------------------------------+
*/

use Phalcon\Logger;

return [
    'application' => [
        'appName'        => 'Cetraria',
        'version'        => '0.0.1',
        'migrationsDir'  => DOCROOT . 'app/migrations/',
        'pidsDir'        => DOCROOT . 'var/pids/',
        'baseUri'        => '/',
        'debug'          => true,
        'titleSeparator' => '::',
    ],
    'i18n' => [
        'default'   => 'en',
        'languages' => [
            'en' => 'English',
            'ru' => 'Pусский',
            'uk' => 'Український',
        ],
    ],
    'volt' => [
        'compiledExt'  => '.php',
        'separator'    => '_',
        'cacheDir'     => DOCROOT . 'var/cache/volt/',
        'forceCompile' => true,
    ],
    'modules' => [
        'user' => true,
        'vcs'  => true,
    ],
    'logger' => [
        'path'     => DOCROOT . 'var/logs/',
        'format'   => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
        'date'     => 'D j H:i:s',
        'logLevel' => Logger::DEBUG,
    ],
    'error' => [
        'logger'    => DOCROOT . 'var/logs/error.log',
        'formatter' => [
            'format' => '%date% ' . HOSTNAME . ' php: [%type%] %message%',
            'date'   => 'D j H:i:s',
        ],
        'controller' => 'error',
        'action'     => 'index'
    ],
    'annotations' => [
        'adapter' => 'Files',
        'annotationsDir' => DOCROOT . 'var/cache/annotations/',
    ],
    'cache' => [
        'adapter' => 'File',
        'lifetime' => 1 * 60 * 60,
        'prefix'   => 'data_',
        'cacheDir' => DOCROOT . 'var/cache/data/'
    ],
    'database' => [
        'adapter'  => 'Postgresql',
        'host'     => 'localhost',
        'username' => 'cetraria',
        'password' => '********',
        'dbname'   => 'cetraria',
        'port'     => 5432,
        'schema'   => 'public'
    ],
    'metadata' => [
        'adapter'     => 'Files',
        'metaDataDir' => DOCROOT . 'var/cache/metadata/',
    ],
    'router' => [
        'cacheKey' => 'router_resources',
        'cacheTtl' => 24 * 60 * 60
    ],
];
