<?php

namespace Cetraria\Library;

use Phalcon\Config;
use Phalcon\DiInterface;
use Phalcon\Events\Manager             as EventsManager;
use Phalcon\Logger\Adapter\File        as FileLogger;
use Phalcon\Logger\Formatter\Line      as FormatterLine;
use Phalcon\Loader;
use Phalcon\Error\Handler              as ErrorHandler;
use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
use Phalcon\Cache\Frontend\None        as FrontNone;
use Phalcon\Cache\Frontend\Output      as FrontOutput;

trait Initializer
{
    protected $loaders = [
        'normal' => [
            'cache',
            'annotations',
        ],
        'cli'    => [],
        'rest'   => [],
    ];

    protected $mode = 'normal';

    /**
     * @param string $mode
     */
    public function init($mode = 'normal')
    {
        if (!isset($this->loaders[$mode])) {
            $mode = 'normal';
        }

        $this->mode = $mode;

        /** @var \Phalcon\DiInterface $di */
        $di = $this->_dependencyInjector;
        $di->setShared('app', $this);

        $eventsManager = new EventsManager;

        $this->initLogger($di, $this->config, $eventsManager);
        $this->initLoader($di, $this->config, $eventsManager);

        $this->setEventsManager($eventsManager);

        foreach ($this->loaders[$mode] as $service) {
            $serviceName = ucfirst($service);
            $this->{'init' . $serviceName}($di, $this->config, $eventsManager);
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
            $configLogger = $config->get('logger')->toArray();

            $path   = $configLogger['path'];
            $date   = $configLogger['date'];
            $format = $format ?: $configLogger['format'];

            $logger = new FileLogger($path . APPLICATION_ENV . '.' . $mode . '.' . $file . '.log');
            $formatter = new FormatterLine($format, $date);
            $logger->setFormatter($formatter);

            return $logger;
        });
    }

    /**
     * Initialize the Loader.
     *
     * Adds all required namespaces and modules. Use 'Cetraria' as common namespace.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return Loader
     */
    protected function initLoader(DiInterface $di, Config $config, EventsManager $em)
    {
        $registry = $di->get('registry');

        $namespaces = [];
        $modules    = [];

        if ('rest' !== $this->mode) {
            $namespaces['Cetraria\Modules'] = $registry->directories->modules;
            foreach ($registry->modules as $module) {
                $moduleName              = 'Cetraria\Modules\\' . ucfirst($module);
                $modules[$module]        = [
                    'className' => $moduleName . '\Module',
                    'path'      => $registry->directories->modules . ucfirst($module) . DS . 'Module.php',
                ];
            }
        }

        $namespaces['Cetraria\Plugins'] = $registry->directories->plugins;
        $namespaces['Cetraria\Library'] = $registry->directories->library;

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
     * @return void
     */
    protected function initCache(DiInterface $di, Config $config, EventsManager $em)
    {
        $cache = function () use ($config) {
            $config  = $config->get('cache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['adapter'];

            if (ENV_PRODUCTION === APPLICATION_ENV) {
                $frontend = new FrontOutput(['lifetime' => $config['lifetime']]);
            } else {
                $frontend = new FrontNone;
            }

            unset($config['adapter'], $config['lifetime']);

            return new $backend($frontend, $config);
        };

        $di->setShared('viewCache', function () use ($cache) {
            return $cache();
        });

        $di->setShared('modelsCache', function () use ($cache) {
            return $cache();
        });
    }

    /**
     * Initialize the Annotations.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initAnnotations(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('annotations', function () use ($config) {
            $annotationsConfig = $config->get('annotations')->toArray();
            if (!$config->get('application')->debug) {
                $annotationsAdapter = '\Phalcon\Annotations\Adapter\\' . $annotationsConfig['adapter'];
                unset($annotationsConfig['adapter']);

                $adapter = new $annotationsAdapter($annotationsConfig);
            } else {
                $adapter = new AnnotationsMemory;
            }

            return $adapter;
        });
    }
}
