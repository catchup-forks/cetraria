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

namespace Cetraria\Library\Listeners;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

/**
 * Action listener
 *
 * @package Cetraria\Library\Listeners
 */
class Action extends Base
{
    /**
     * @param Event      $event
     * @param Dispatcher $dispatcher
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $message = sprintf(
            '%s::%s() started.',
            get_class($dispatcher->getActiveController()),
            $dispatcher->getActiveMethod()
        );

        $this->logger->debug($message);
    }

    /**
     * @param Event      $event
     * @param Dispatcher $dispatcher
     */
    public function afterExecuteRoute(Event $event, Dispatcher $dispatcher)
    {
        $message = sprintf(
            '%s::%s() finished.',
            get_class($dispatcher->getActiveController()),
            $dispatcher->getActiveMethod()
        );

        $this->logger->debug($message);
    }
}
