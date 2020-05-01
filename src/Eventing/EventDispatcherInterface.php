<?php

declare(strict_types=1);

namespace Domynation\Eventing;

/**
 * Interface EventDispatcherInterface
 *
 * @package Domynation\Eventing
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface EventDispatcherInterface
{
    public const PRIORITY_HIGH   = 300;
    public const PRIORITY_MEDIUM = 200;
    public const PRIORITY_LOW    = 100;

    /**
     * Raises an event.
     *
     * @param Event $event
     */
    public function raise(Event $event): void;

    /**
     * Adds an event listener. A numeric priority can be provided.
     *
     * @param string $eventName
     * @param callable $callable
     * @param int $priority
     */
    public function listen($eventName, callable $callable, $priority = null): void;

    /**
     * Dispatches all raised events and clear the list of raised events.
     */
    public function dispatch(): void;

    /**
     * Clears the list of raised events.
     */
    public function clearEvents(): void;

    /**
     * Returns all the events that have been raised.
     *
     * @return array|\Domynation\Eventing\Event[]
     */
    public function getRaisedEvents(): array;

    /**
     * Returns all listeners. If an event name is provided, only the listeners
     * for this specific event are returned.
     *
     * @param string $eventName
     *
     * @return array
     */
    public function getListeners($eventName = null): array;
}