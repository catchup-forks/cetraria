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

/**
 * The Command Runner Interface
 *
 * @package   Cetraria\Library\Cli
 * @copyright Copyright (c) 2011-2015 Phalcon Team (team@phalconphp.com)
 * @license   New BSD License
 */
interface RunnerInterface
{
    /**
     * Adds commands to the stack.
     *
     * @param CommandInterface $command
     * @return $this
     */
    public function attach(CommandInterface $command);

    /**
     * Gets the Commands registered in the Command Runner.
     *
     * @return CommandInterface[]
     */
    public function getCommands();
}
