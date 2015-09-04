<?php

namespace Cetraria\Modules\Core;

use Phalcon\Mvc\View;
use Phalcon\DiInterface;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        $di->setShared('view', function () use ($di) {
            $view   = new View;

            $config    = $di->get('config');
            $em        = $di->get('eventsManager');
            $directory = $di->get('registry')->directories->modules . 'Core/';

            $view->registerEngines([
                '.volt'  => function ($view, $di) use ($config) {
                    $volt   = new Volt($view, $di);
                    $config = $config->get('volt')->toArray();

                    $options = [
                        'compiledPath'      => $config['cacheDir'],
                        'compiledExtension' => $config['compiledExt'],
                        'compiledSeparator' => $config['separator'],
                        'compileAlways'     => ENV_DEVELOPMENT === APPLICATION_ENV,
                    ];

                    $volt->setOptions($options);

                    return $volt;
                },
                '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
            ]);

            $view
                ->setViewsDir($directory . 'Views/')
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
                    throw new \Exception($message);
                }
            });

            $view->setEventsManager($em);

            return $view;
        });
    }
}
