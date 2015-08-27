<?php

namespace Cetraria\Library\Listeners;

use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;

class Initializer extends Injectable
{
    public function __construct(DiInterface $di, EventsManager $em)
    {
        $this->setDI($di);
        $this->setEventsManager($em);
    }

    public function beforeCache(Event $event, $source, $mode)
    {
        if ($this->config->get('application')->debug) {
            if (is_object($source)) {
                $source = get_class($source);
            }

            /** @var \Phalcon\Logger\Adapter\File $logger */
            $logger = $this->di->get('logger', ['init']);
            $logger->debug(sprintf('%s: Init cache from %s as %s mode', $event->getType(), $source, $mode));
        }

        return true;
    }
}
