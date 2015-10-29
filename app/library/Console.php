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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use FilesystemIterator;
use Phalcon\Config;
use Phalcon\Registry;
use Phalcon\DiInterface;
use Phalcon\Cli\Console           as PhConsole;
use Phalcon\Di\FactoryDefault\Cli as CliDi;
use Phalcon\Events\Manager        as EventsManager;
use Cetraria\Console\Commands\CommandsListener;
use Cetraria\Console\Commands\CommandInterface;
use Cetraria\Console\CommandRunner;
use Cetraria\Console\RunnerInterface;

class Console extends PhConsole
{
    use Initializer;

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
                ['core'],
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
     * Runt the CommandRunner
     *
     * @param  array $argv Array of arguments passed to the Application
     * @param  int   $argc The number of arguments passed to the Application
     */
    public function run(array $argv, $argc)
    {
        /** @var RunnerInterface $runner */
        $runner = $this->getDI()->get('runner');

        $runner->setArgs($argv, $argc);
        $runner->run();
    }

    /**
     * Initialize Commands.
     *
     * Recursively looks for Commands and attach them to the CommandRunner.
     *
     * @param DiInterface   $di     Dependency Injector
     * @param Config        $config App config
     * @param EventsManager $em     Events Manager
     *
     * @return void
     */
    protected function initCommands(DiInterface $di, Config $config, EventsManager $em)
    {
        $di->setShared('runner', function () use ($di, $config, $em) {
            $runner = new CommandRunner(
                $config->get('application')->appName,
                $config->get('application')->version
            );

            $em->attach('command', new CommandsListener);

            $allModules = $di->get('registry')->modules;

            foreach ($allModules as $module) {
                $moduleName = ucfirst($module);
                $commandDir = $di->get('registry')->directories->modules . $moduleName . '/Commands';

                if (is_readable($commandDir) && is_dir($commandDir)) {
                    $namespace = "Cetraria\\Modules\\{$moduleName}\\Commands";

                    $directory = new RecursiveDirectoryIterator($commandDir);
                    $directory->setFlags(FilesystemIterator::SKIP_DOTS);

                    $iterator = new RecursiveIteratorIterator($directory);
                    $iterator->rewind();

                    while ($iterator->valid()) {
                        if (false !== strpos($iterator->getBasename(), 'Command.php')) {
                            $baseName = $iterator->getBasename();
                            $commandFile = substr($iterator->getPathname(), strlen($commandDir) + 1);

                            $possibleClass = substr($commandFile, 0, -(strlen($baseName) + 1)) . '\\' . $iterator->getBasename('.php');
                            $possibleClass = str_replace('/', '\\', $namespace . '\\' . trim($possibleClass, '\\/'));

                            // All magic is here
                            if (class_exists($possibleClass)) {
                                $command = new $possibleClass($runner);

                                if ($command instanceof CommandInterface) {
                                    $command->setDI($di);
                                    $runner->attach($command);
                                }
                            }
                        }

                        $iterator->next();
                    }
                }
            }

            $runner->setEventsManager($em);
            $runner->setDI($di);

            return $runner;
        });
    }
}
