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

namespace Cetraria\Library\Cli;

use Cetraria\Library\Cli\Commands\CommandInterface;
use Phalcon\Di\Injectable;

class CommandRunner extends Injectable
{
    /**
     * Array of arguments passed to the Application
     * @var array
     */
    protected $argv = [];

    /**
     * The number of arguments passed to the Application
     * @var int
     */
    protected $argc = 0;

    /**
     * Current commands
     * @var CommandInterface[]
     */
    protected $commands = [];

    /**
     * Adds commands to the stack
     *
     * @param CommandInterface $command
     * @return $this
     */
    public function attach(CommandInterface $command)
    {
        $this->commands[spl_object_hash($command)] = $command;

        return $this;
    }

    /**
     * Set Application arguments
     *
     * @param  array $argv Array of arguments passed to the Application
     * @param  int   $argc The number of arguments passed to the Application
     * @return $this
     */
    public function setArgs(array $argv, $argc)
    {
        $this->argv = $argv;
        $this->argc = $argc;

        return $this;
    }

    /**
     * Returns the commands registered in the script
     *
     * @return CommandInterface[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Run Commands
     *
     * @throws RunnerException
     */
    public function run()
    {
        echo 'We are here', PHP_EOL;
    }
}
