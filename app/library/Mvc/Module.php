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

namespace Cetraria\Library\Mvc;

use Cetraria\Library\DiInjector;
use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Exception;
use Phalcon\Mvc\View\Exception as ViewException;
use Phalcon\Tag;

abstract class Module implements ModuleDefinitionInterface
{
    use DiInjector {
        DiInjector::__construct as injectDi;
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
                ->modules . ucfirst($this->getModuleName()) . '/';

        $this->viewsDirectory = $this->getModuleDirectory() . 'Views/';
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

                    $volt->getCompiler()
                        ->addFunction('full_title', function () use ($config) {
                            $title = Tag::getTitle(false);
                            $appName = $config->get('application')->appName;

                            if (empty(trim($title))) {
                                return "'<title>$appName</title>'";
                            }

                            $titleSeparator = $config->get('application')->titleSeparator;
                            return "'<title>$appName $titleSeparator $title</title>'";
                        });

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
                if ($config->application->debug) {
                    $logger = $di->get('logger', ['view']);
                    $logger->debug(sprintf('Event %s. Path: %s', $event->getType(), $view->getActiveRenderPath()));
                }

                if ('notFoundView' == $event->getType()) {
                    $message = sprintf('View not found: %s', $view->getActiveRenderPath());
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

    public function getViewDirectory()
    {
        return $this->viewsDirectory;
    }

    /**
     * Get module name
     *
     * @return null|string
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
}
