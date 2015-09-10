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

use Phalcon\Mvc\ModuleDefinitionInterface;

interface ModuleInterface extends ModuleDefinitionInterface
{
    /**
     * Get module name
     *
     * @return string
     * @throws \Phalcon\Exception
     */
    public function getModuleName();
}
