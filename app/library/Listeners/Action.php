<?php

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
