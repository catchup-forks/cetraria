<?php

namespace Cetraria\Library\Listeners;

use Phalcon\DiInterface;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Logger\Formatter\Line as FormatterLine;
use Phalcon\Di\Injectable;

/**
 * Abstract listener
 *
 * @package Cetraria\Library\Listeners
 */
abstract class Base extends Injectable
{
    /**
     * Instance of the Logger
     * @var \Phalcon\Logger\AdapterInterface
     */
    protected $logger = null;

    /**
     * Instance of the config
     * @var \Phalcon\Config
     */
    protected $config = null;

    /**
     * Is debug mode?
     * @var bool
     */
    protected $debug = false;

    public function __construct(DiInterface $di, EventsManager $em)
    {
        $this->setDI($di);
        $this->setEventsManager($em);

        $this->config = $di->get('config');

        $this->logger = $di->get('logger', ['debug']);
        $this->logger->setFormatter(
            new FormatterLine(
                $this->config->logger->format,
                $this->config->logger->date
            )
        );

        $this->debug = (bool) $this->config->get('application')->debug;
    }
}
