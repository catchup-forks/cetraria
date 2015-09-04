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

namespace Cetraria\Library;

use Phalcon\Di;
use Phalcon\DiInterface;

trait DiInjector
{
    /**
     * Dependency injection container.
     *
     * @var DiInterface
     */
    private $di = null;

    /**
     * Create object.
     *
     * @param DiInterface $di Dependency injection container
     */
    public function __construct(DiInterface $di = null)
    {
        $this->setDI($di ?: Di::getDefault());
    }

    /**
     * Set dependency injection container
     *
     * @param DiInterface $di Dependency injection container
     * @return $this
     */
    public function setDI(DiInterface $di)
    {
        $this->di = $di;

        return $this;
    }

    /**
     * Get dependency injection container
     *
     * @return DiInterface
     */
    public function getDI()
    {
        return $this->di;
    }
}
