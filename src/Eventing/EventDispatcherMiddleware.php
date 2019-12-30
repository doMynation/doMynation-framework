<?php

namespace Domynation\Eventing;

use Domynation\Eventing\Event;

/**
 * @package Domynation\Eventing
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class EventDispatcherMiddleware
{

    /**
     * The next middleware in the chain.
     */
    protected ?EventDispatcherMiddleware $next;

    /**
     * Handles the event.
     *
     * @param \Domynation\Eventing\Event $event
     */
    abstract public function handle(Event $event): void;

    /**
     * Sets the next middleware to handle the command
     *
     * @param \Domynation\Eventing\EventDispatcherMiddleware|null $middleware
     */
    public function setNext(?EventDispatcherMiddleware $middleware = null)
    {
        $this->next = $middleware;
    }
}
