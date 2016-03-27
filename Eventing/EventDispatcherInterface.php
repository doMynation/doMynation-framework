<?php

namespace Domynation\Eventing;


interface EventDispatcherInterface
{

    const PRIORITY_HIGH   = 300;
    const PRIORITY_MEDIUM = 200;
    const PRIORITY_LOW    = 100;

    /**
     * Raises an event.
     *
     * @param Event $event
     *
     * @return void
     */
    public function raise(Event $event);

    /**
     * Adds an event listener. A numeric priority can be provided.
     *
     * @param string $eventName
     * @param callable $callable
     * @param int $priority
     *
     * @return void
     */
    public function listen($eventName, callable $callable, $priority = null);

    /**
     * Dispatches all raised events and clear the list of raised
     * events.
     *
     * @return void
     */
    public function dispatch();

    /**
     * Returns all the events that have been raised.
     *
     * @return Event[]
     */
    public function getRaisedEvents();

    /**
     * Returns all listeners. If an event name is provided, only the listeners
     * for this specific event are returned.
     *
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners($eventName = null);
}