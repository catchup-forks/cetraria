<?php

namespace Library;

use Phalcon\Mvc\Application as PhApplication;
use Phalcon\Di\FactoryDefault;
use Phalcon\DiInterface;
use Phalcon\Config;
use Phalcon\Registry;

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

        $this->config = new Config(require_once BASE_DIR . 'config' . DS . APPLICATION_ENV . '.php');

        // Setup Registry
        $registry = new Registry;
        $registry->modules = array_merge(
            [self::DEFAULT_MODULE, 'user'],
            $this->config->modules->toArray()
        );

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
}
