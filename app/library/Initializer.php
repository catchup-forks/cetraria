<?php

namespace Library;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Adapter\File as FileLogger;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Loader;
use Phalcon\Error\Handler as ErrorHandler;

trait Initializer
{
    protected $loaders = [
        'normal' => [
            'logger',
            'loader',
            'cache',
        ],
        'cli'    => [],
        'rest'   => [],
    ];

    protected $mode = 'normal';

    public function init($mode = 'normal')
    {
        if (!isset($this->loaders[$mode])) {
            $mode = 'normal';
        }
        $this->mode = $mode;

        // Set application main objects.
        $di = $this->_dependencyInjector;
        $di->setShared('app', $this);

        $eventsManager = new EventsManager;
        $this->setEventsManager($eventsManager);

        foreach ($this->loaders[$mode] as $service) {
            $serviceName = ucfirst($service);
            $eventsManager->fire('init:before' . $serviceName, null);
            $result = $this->{'init' . $serviceName}($di, $this->config, $eventsManager);
            $eventsManager->fire('init:after' . $serviceName, $result);
        }

        $di->setShared('eventsManager', $eventsManager);
    }

    /**
     * Initialize the Logger.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initLogger(DiInterface $di, Config $config, EventsManager $em)
    {
        ErrorHandler::register();

        $mode = $this->mode;
        $di->set('logger', function ($file = 'main', $format = null) use ($config, $mode) {
            $path   = $config->logger->path;
            $date   = $config->logger->date;
            $format = $format ?: $config->logger->format;

            $logger = new FileLogger($path . APPLICATION_ENV . '.' . $mode . '.' . $file . '.log');
            $formatter = new FormatterLine($format, $date);
            $logger->setFormatter($formatter);

            return $logger;
        });
    }

    /**
     * Initialize the Loader.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return Loader
     */
    protected function initLoader(DiInterface $di, Config $config, EventsManager $em)
    {
        // Add all required namespaces and modules.
        $registry = $di->get('registry');

        $namespaces = [];
        $bootstraps = [];

        foreach ($registry->modules as $module) {
            $moduleName = ucfirst($module);
            $namespaces[$moduleName] = $registry->directories->modules . $moduleName . DS;
            $bootstraps[$module] = $moduleName . '\Bootstrap';
        }

        $namespaces['Plugin']  = $registry->directories->plugins;
        $namespaces['Library'] = $registry->directories->library;

        $loader = new Loader;
        $loader->registerNamespaces($namespaces);

        if ($config->application->debug) {
            $em->attach('loader', function ($event, $loader) use ($di) {
                if ($event->getType() == 'beforeCheckPath') {
                    $logger = $di->get('logger', ['autoload']);
                    $logger->debug('Before check path: '. $loader->getCheckedPath());
                }
            });
        }

        $loader->setEventsManager($em);

        $loader->register();
        $this->registerModules($bootstraps);

        $di->setShared('loader', $loader);

        return $loader;
    }

    /**
     * Initialize the Cache.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return Loader
     */
    protected function initCache(DiInterface $di, Config $config, EventsManager $em)
    {
        $cacheConfig  = $config->cache->toArray();
        $cacheAdapter = '\Phalcon\Cache\Backend\\' . $cacheConfig['adapter'];
        unset($cacheConfig['adapter']);
    }
}
