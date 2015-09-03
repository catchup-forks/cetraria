<?php

namespace Cetraria\Library;

use Phalcon\Config;
use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DiInterface;
use Phalcon\Events\Manager             as EventsManager;
use Phalcon\Logger\Adapter\File        as FileLogger;
use Phalcon\Logger\Formatter\Line      as FormatterLine;
use Phalcon\Error\Handler              as ErrorHandler;
use Phalcon\Annotations\Adapter\Memory as AnnotationsMemory;
use Phalcon\Cache\Frontend\None        as FrontNone;
use Phalcon\Cache\Frontend\Output      as FrontOutput;
use Phalcon\Cache\Frontend\Data        as FrontData;
use Phalcon\Mvc\Model\Manager          as ModelsManager;
use Phalcon\Mvc\Model\MetaData\Memory  as MetaData;
use Phalcon\Mvc\Router\Annotations     as AnnotationsRouter;

/**
 * Application Initializer
 *
 * @package Cetraria\Library
 */
trait Initializer
{
    protected $loaders = [
        'normal' => [
            'cache',
            'annotations',
            'database',
            'router'
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
            $config = $config->get('logger')->toArray();
            $path   = $config['path'];
            $date   = $config['date'];
            $format = $format ?: $config['format'];

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

        if (ENV_DEVELOPMENT === APPLICATION_ENV) {
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
        $backend = function ($frontend, $config) {
            $config  = $config->get('cache')->toArray();
            $backend = '\Phalcon\Cache\Backend\\' . $config['adapter'];
            unset($config['adapter'], $config['lifetime']);

            return new $backend($frontend, $config);
        };

        $data = function () use ($config) {
            $config  = $config->get('cache')->toArray();

            return new FrontData(['lifetime' => $config['lifetime']]);
        };

        $output = function () use ($config) {
            $config  = $config->get('cache')->toArray();

            return new FrontOutput(['lifetime' => $config['lifetime']]);
        };

        $di->setShared('viewCache', function () use ($output, $backend, $config) {
            return $backend(
                ENV_PRODUCTION === APPLICATION_ENV ? $output() : new FrontNone,
                $config
            );
        });

        $di->setShared('modelsCache', function () use ($data, $backend, $config) {
            return $backend(
                ENV_PRODUCTION === APPLICATION_ENV ? $data() : new FrontNone,
                $config
            );
        });

        $di->setShared('dataCache', function () use ($data, $backend, $config) {
            return $backend(
                ENV_PRODUCTION === APPLICATION_ENV ? $data() : new FrontNone,
                $config
            );
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
            if (ENV_PRODUCTION === APPLICATION_ENV) {
                $config  = $config->get('annotations')->toArray();
                $adapter = '\Phalcon\Annotations\Adapter\\' . $config['adapter'];
                unset($config['adapter']);

                $adapter = new $adapter($config);
            } else {
                $adapter = new AnnotationsMemory;
            }

            return $adapter;
        });
    }

    /**
     * Initialize the Database connection.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initDatabase(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('db', function () use ($config) {
            $config  = $config->get('database')->toArray();
            $adapter = '\Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
            unset($config['adapter']);

            $connection = new $adapter($config);

            return $connection;
        });

        $di->setShared('modelsManager', function () use ($config, $em) {
            $modelsManager = new ModelsManager;
            $modelsManager->setEventsManager($em);

            return $modelsManager;
        });

        $di->setShared('modelsMetadata', function () use ($config, $em) {
            if (ENV_PRODUCTION === APPLICATION_ENV) {
                $config = $config->get('metadata')->toArray();
                $adapter = '\Phalcon\Mvc\Model\Metadata\\' . $config['adapter'];
                unset($config['adapter']);

                $metaData = new $adapter($config);
            } else {
                $metaData = new MetaData;
            }

            return $metaData;
        });
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
        $di->setShared('router', function () use ($di, $config, $em) {
            $cache  = $di->get('dataCache');
            $router = $cache->get('router_data');

            if (ENV_DEVELOPMENT === APPLICATION_ENV || !$router) {
                $save   = !$router;
                $router = new AnnotationsRouter(false);

                $moduleName = Application::DEFAULT_MODULE;
                $namespace  = 'Cetraria\Modules\\' . ucfirst($moduleName) . '\Controllers';
                $allModules = $di->get('registry')->modules;

                if (!isset($_GET['_url'])) {
                    $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
                }

                $router->setDefaultModule($moduleName);
                $router->setDefaultNamespace($namespace);
                $router->setDefaultController('Index');
                $router->setDefaultAction('index');
                $router->removeExtraSlashes(true);
                $router->setEventsManager($em);

                foreach ($allModules as $module) {
                    $moduleName = ucfirst($module);
                    $dir = new \DirectoryIterator($di->get('registry')->directories->modules . $moduleName);

                    foreach ($dir as $fileInfo) {
                        if ($fileInfo->isDot() || false === strpos($fileInfo->getBasename(), 'Controller.php')) {
                            continue;
                        }

                        $controller = $namespace . '\\' .$fileInfo->getBasename('Controller.php');
                        $router->addModuleResource(strtolower($module), $controller);
                    }
                }

                if ($save) {
                    $cache->save('router_data', $router, $config->get('router')->cacheTtl);
                }
            }

            return $router;
        });
    }
}
