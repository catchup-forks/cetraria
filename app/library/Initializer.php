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
            $eventsManager->fire('init:beforeInit' . $serviceName, $this);
            $result = $this->{'init' . $serviceName}($di, $this->config, $eventsManager);
            $eventsManager->fire('init:afterInit' . $serviceName, $this, $result, false);
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
        $modules    = [];

        if ('rest' !== $this->mode) {
            foreach ($registry->modules as $module) {
                $moduleName              = ucfirst($module);
                $namespaces[$moduleName] = $registry->directories->modules . $moduleName . DS;
                $modules[$module]        = [
                    'className' => $moduleName . '\Module',
                    'path'      => $namespaces[$moduleName] . 'Module.php',
                ];
            }
        }

        $namespaces['Plugin']  = $registry->directories->plugins;
        $namespaces['Library'] = $registry->directories->library;

        $loader = new Loader;
        $loader->registerNamespaces($namespaces);

        if ($config->get('application')->debug) {
            $em->attach('loader', function ($event, $loader) use ($di) {
                /**
                 * @var \Phalcon\Events\Event $event
                 * @var \Phalcon\Loader $loader
                 * @var \Phalcon\Logger\Adapter\File $logger
                 */
                $logger = $di->get('logger', ['autoload']);

                if ('beforeCheckPath' == $event->getType()) {
                    $logger->debug('Before check path: ' . $loader->getCheckedPath());
                }

                if ('pathFound' == $event->getType()) {
                    $logger->debug('Path found: ' . $loader->getFoundPath());
                }

                if ('afterCheckClass' == $event->getType()) {
                    $data = [
                        'classes'    => $loader->getClasses(),
                        'namespaces' => $loader->getNamespaces(),
                        'dirs'       => $loader->getDirs(),
                    ];

                    // From Phalcon 2.1.0 version has been removed support for prefixes strategy
                    // @deprecated
                    if (method_exists($loader, 'getPrefixes')) {
                        $data['prefixes'] = $loader->getPrefixes();
                    }

                    $logger->debug(
                        'Class not found. Current loader settings: ' .
                        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    );
                }
            });
        }

        $loader->setEventsManager($em);

        $loader->register();

        if ('rest' !== $this->mode) {
            $this->registerModules($modules);
        }

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
