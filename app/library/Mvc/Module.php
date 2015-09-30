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

namespace Cetraria\Library\Mvc;

use Cetraria\Library\DiInjector;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Exception;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Tag;
use Phalcon\Config;

abstract class Module implements ModuleInterface
{
    use DiInjector {
        DiInjector::__construct as protected injectDi;
    }

    /**
     * Module name
     * @var string|null
     */
    protected $moduleName = null;

    /**
     * Views directory
     * @var string|null
     */
    protected $viewsDirectory = null;

    /**
     * Module directory
     * @var string|null
     */
    protected $moduleDirectory = null;

    /**
     * Module config directory
     * @var string|null
     */
    protected $configDirectory = null;

    /**
     * Module Constructor
     *
     * @throws \Phalcon\Exception
     */
    public function __construct()
    {
        $this->injectDi();

        $this->moduleDirectory = $this->getDI()
                ->get('registry')
                ->directories
                ->modules . ucfirst($this->getModuleName()) . DIRECTORY_SEPARATOR;

        $this->viewsDirectory  = $this->getModuleDirectory() . 'Views' . DIRECTORY_SEPARATOR;
        $this->configDirectory = $this->getModuleDirectory() . 'Config' . DIRECTORY_SEPARATOR;
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     * @throws \Phalcon\Exception
     * @throws \Phalcon\Mvc\View\Exception
     */
    public function registerServices(DiInterface $di)
    {
        $viewsDirectory = $this->viewsDirectory;

        $this->loadConfig();

        $di->setShared('tag', function () use ($di) {
            $config = $di->get('config');

            $tag = new Tag;
            $tag->setDocType(Tag::HTML5);
            $tag->setTitleSeparator($config->get('application')->get('titleSeparator', ' :: '));
            $tag->setTitle($config->get('application')->get('appName', 'Cetraria'));

            return $tag;
        });

        $di->setShared('view', function () use ($di, $viewsDirectory) {
            $view   = new View;
            $config = $di->get('config');
            $em     = $di->get('eventsManager');

            $view->registerEngines([
                '.volt'  => function ($view, $di) use ($config) {
                    $volt   = new Volt($view, $di);
                    $voltConfig = $config->get('volt')->toArray();

                    $options = [
                        'compiledPath'      => $voltConfig['cacheDir'],
                        'compiledExtension' => $voltConfig['compiledExt'],
                        'compiledSeparator' => $voltConfig['separator'],
                        'compileAlways'     => ENV_DEVELOPMENT === APPLICATION_ENV && $voltConfig['forceCompile'],
                    ];

                    $volt->setOptions($options);
                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ]);

            $view
                ->setViewsDir($viewsDirectory)
                ->disableLevel([View::LEVEL_LAYOUT => true]);

            $em->attach('view', function ($event, $view) use ($di, $config) {
                /**
                 * @var \Phalcon\Logger\AdapterInterface $logger
                 * @var \Phalcon\Mvc\View $view
                 * @var \Phalcon\Events\Event $event
                 */
                $logger = $di->get('logger');
                $logger->debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()));

                if ('notFoundView' == $event->getType()) {
                    $message = sprintf('View not found: %s', $view->getActiveRenderPath());
                    $logger->error($message);
                    throw new ViewException($message);
                }
            });

            $view->setEventsManager($em);

            return $view;
        });
    }

    /**
     * Get module directory
     *
     * @return string
     */
    public function getModuleDirectory()
    {
        return $this->moduleDirectory;
    }

    /**
     * Get view directory
     *
     * @return null|string
     */
    public function getViewDirectory()
    {
        return $this->viewsDirectory;
    }

    /**
     * Get module name
     *
     * @return string
     * @throws \Phalcon\Exception
     */
    public function getModuleName()
    {
        if (empty($this->moduleName)) {
            $class = new \ReflectionClass($this);
            throw new Exception('Module has no module name: ' . $class->getFileName());
        }

        return $this->moduleName;
    }

    /**
     * Loads and merges module-specific config
     */
    protected function loadConfig()
    {
        $config = $this->getDI()->getShared('config');

        if (is_readable($this->configDirectory . 'config.php')) {
            $moduleConfig = include_once $this->configDirectory . 'config.php';

            if (is_array($moduleConfig)) {
                $moduleConfig = new Config($moduleConfig);
            }

            if ($moduleConfig instanceof Config) {
                $config->merge($moduleConfig);
            }
        }

        if (is_readable($this->configDirectory . APPLICATION_ENV . '.php')) {
            $override = include_once $this->configDirectory . APPLICATION_ENV . '.php';

            if (is_array($override)) {
                $override = new Config($override);
            }

            if ($override instanceof Config) {
                $config->merge($override);
            }
        }
    }
}
