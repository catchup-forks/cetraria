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

namespace Cetraria\Library;

use Phalcon\Config;
use Phalcon\Registry;
use Phalcon\DiInterface;
use Phalcon\Cli\Console           as PhConsole;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Events\Manager        as EventsManager;


class Console extends PhConsole
{
    use Initializer;

    const DEFAULT_MODULE = 'core';

    /**
     * Array of arguments passed to the Application
     * @var array
     */
    protected $argv = [];

    /**
     * The number of arguments passed to the Application
     * @var int
     */
    protected $argc = 0;

    /**
     * Cetraria\Library\Console constructor
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

        });

        parent::__construct($di);
    }

    /**
     * Set Application arguments
     *
     * @param  array $argv Array of arguments passed to the Application
     * @param  int   $argc The number of arguments passed to the Application
     * @return $this
     */
    public function setArgs(array $argv, $argc)
    {
        $this->argv = $argv;
        $this->argc = $argc;

        return $this;
    }

    /**
     * Initialize the Router.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initRouter(DiInterface $di, Config $config, EventsManager $em)
    {
    }
}
