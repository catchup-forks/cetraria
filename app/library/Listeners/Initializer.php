<?php

namespace Cetraria\Library\Listeners;

use Phalcon\Events\Event;

/**
 * Initializer listener
 *
 * @package Cetraria\Library\Listeners
 */
class Initializer extends Base
{
    public function beforeCache(Event $event, $source, $mode)
    {
        if (is_object($source)) {
            $source = get_class($source);
        }

        $this->logger->debug(sprintf('%s: Init Cache from %s as %s mode', $event->getType(), $source, $mode));

        return true;
    }

    public function beforeAnnotations(Event $event, $source, $mode)
    {
        if (is_object($source)) {
            $source = get_class($source);
        }

        $this->logger->debug(sprintf('%s: Init Annotations from %s as %s mode', $event->getType(), $source, $mode));

        return true;
    }
}
