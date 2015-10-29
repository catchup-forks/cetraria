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
    protected $commands = [];

    /**
     * Adds commands to the stack
     *
     * @param CommandInterface $command
     */
    public function attach(CommandInterface $command)
    {
        $this->commands[spl_object_hash($command)] = $command;
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
}
