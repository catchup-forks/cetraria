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

namespace Cetraria\Library\Cli\Commands;

use Phalcon\Di\Injectable;
use Cetraria\Library\Cli\RunnerInterface;

/**
 * Abstract Command
 *
 * @package   Cetraria\Library\Cli\Commands
 * @copyright Copyright (c) 2011-2015 Phalcon Team (team@phalconphp.com)
 * @license   New BSD License
 */
abstract class Command extends Injectable implements CommandInterface
{
    /**
     * The Command Runner
     * @var RunnerInterface
     */
    protected $runner;

    /**
     * The Command Constructor.
     *
     * @param RunnerInterface $runner
     */
    final public function __construct(RunnerInterface $runner)
    {
        $this->runner = $runner;
    }

    /**
     * Gets the Command Runner.
     *
     * @return RunnerInterface
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * Sets the Command Runner.
     *
     * @param RunnerInterface $runner The Command Runner
     * @return $this
     */
    public function setRunner(RunnerInterface $runner)
    {
        $this->runner = $runner;

        return $this;
    }
}
