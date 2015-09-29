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
 | to me@klay.me so I can send you a copy immediately.                    |
 +------------------------------------------------------------------------+
*/

namespace Cetraria\Library;

use Phalcon\Cli\Console as PhConsole;
use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Registry;

class Console extends PhConsole
{
    use Initializer;

    const DEFAULT_MODULE = 'core';

    /**
     * Phalcon\Cli\Console constructor
     *
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di = null)
    {
        $di = $di ?: new CliDi;

        // Store config in the DI container
        $di->setShared('config', $this->initConfig());

        $di->setShared('registry', function () use ($di) {
            $config = $di->getShared('config');

            // Setup Registry
            $registry = new Registry;
            $registry->offsetSet('modules', array_merge(
                [self::DEFAULT_MODULE],
                array_keys(array_filter($config->get('modules')->toArray()))
            ));

            $registry->offsetSet('directories', (object)[
                'modules' => DOCROOT . 'app/modules/',
                'plugins' => DOCROOT . 'app/plugins/',
                'library' => DOCROOT . 'app/library/'
            ]);

            return $registry;

        });;

        parent::__construct($di);
    }
}