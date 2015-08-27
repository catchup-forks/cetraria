<?php

namespace Cetraria\Library\Listeners;

use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Logger\Formatter\Line as FormatterLine;

/**
 * Class Initializer
 * @package Cetraria\Library\Listeners
 *
 * @property \Phalcon\Config config
 */
class Initializer extends Injectable
{
    /**
     * @var \Phalcon\Logger\Adapter\File
     */
    protected $logger;

    public function __construct(DiInterface $di, EventsManager $em)
    {
        $this->setDI($di);
        $this->setEventsManager($em);

        $this->logger = $di->get('logger', ['init']);
        $this->logger->setFormatter(new FormatterLine(
                $di->get('config')->logger->format,
                $di->get('config')->logger->date
            )
        );
    }

    public function beforeCache(Event $event, $source, $mode)
    {
        if ($this->config->get('application')->debug) {
            if (is_object($source)) {
                $source = get_class($source);
            }

            $this->logger->debug(sprintf('%s: Init cache from %s as %s mode', $event->getType(), $source, $mode));
        }

        return true;
    }
}
