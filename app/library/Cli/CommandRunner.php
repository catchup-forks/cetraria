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

/**
 * Command Runner
 *
 * @package   Cetraria\Library\Cli
 * @copyright Copyright (c) 2011-2015 Phalcon Team (team@phalconphp.com)
 * @license   New BSD License
 */
class CommandRunner extends Injectable implements RunnerInterface
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
     * Application name
     * @var string
     */
    protected $name;

    /**
     * Application version
     * @var string
     */
    protected $version;

    /**
     * Runner Constructor.
     *
     * @param string $name    The name of the application
     * @param string $version The version of the application
     */
    public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
    {
        $this->name = $name;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $name The application name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $version The application version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     *
     * @return CommandInterface[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     *
     * @throws RunnerException
     */
    public function run()
    {
        echo 'We are here', PHP_EOL;
    }
}
