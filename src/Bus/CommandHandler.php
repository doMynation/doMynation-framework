<?php

declare(strict_types=1);

namespace Domynation\Bus;

use Domynation\Eventing\EventDispatcherInterface;

/**
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class CommandHandler
{
    protected ?EventDispatcherInterface $dispatcher;

    /**
     * Sets the event dispatcher for this command handler.
     *
     * @param \Domynation\Eventing\EventDispatcherInterface $dispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }
}