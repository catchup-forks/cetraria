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

use Phalcon\Mvc\Application as PhApplication;
use Phalcon\Di\FactoryDefault;
use Phalcon\DiInterface;
use Phalcon\Config;
use Phalcon\Registry;

/**
 * Application class
 *
 * @package Cetraria\Library
 */
class Application extends PhApplication
{
    use Initializer;

    const DEFAULT_MODULE = 'core';

    const ENV_PRODUCTION  = ENV_PRODUCTION;
    const ENV_STAGING     = ENV_STAGING;
    const ENV_TEST        = ENV_TEST;
    const ENV_DEVELOPMENT = ENV_DEVELOPMENT;

    /**
     * Application config
     * @var Config
     */
    protected $config = null;

    /**
     * Application constructor
     *
     * @param mixed $di Dependency Injector
     */
    public function __construct(DiInterface $di = null)
    {
        $di = $di ?: new FactoryDefault;

        $this->config = $this->parseConfig();

        $modules = array_filter($this->config->modules->toArray());

        // Setup Registry
        $registry = new Registry;
        $registry->modules = array_merge([self::DEFAULT_MODULE], array_keys($modules));

        $registry->directories = (object)[
            'modules' => BASE_DIR . 'app/modules/',
            'plugins' => BASE_DIR . 'app/plugins/',
            'library' => BASE_DIR . 'app/library/'
        ];

        $di->setShared('registry', $registry);

        // Store config in the DI container
        $di->setShared('config', $this->config);

        parent::__construct($di);
    }

    /**
     * Runs the Application
     *
     * @return $this|string
     */
    public function run()
    {
        if (ENV_TEST === APPLICATION_ENV) {
            return $this;
        }

        return $this->getOutput();
    }

    /**
     * Get application output.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->handle()->getContent();
    }
}
