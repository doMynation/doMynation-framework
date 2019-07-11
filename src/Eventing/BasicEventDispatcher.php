<?php

namespace Domynation\Eventing;

use Invoker\InvokerInterface;

/**
 * A basic implementation of the event dispatcher.
 *
 * @package Domynation\Eventing
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class BasicEventDispatcher implements EventDispatcherInterface
{

    /**
     * @var \Invoker\InvokerInterface
     */
    private $invoker;

    /**
     * @var array
     */
    private $listeners;

    /**
     * @var Event[]
     */
    private $raisedEvents;

    /**
     * BasicEventDispatcher constructor.
     *
     * @param \Invoker\InvokerInterface $invoker
     */
    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
        $this->listeners = [];
        $this->raisedEvents = [];
    }

    /**
     * {@inheritdoc}
     */
    public function raise(Event $event, $priority = null)
    {
        $this->raisedEvents[] = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function listen($eventName, callable $callable, $priority = null)
    {
        $listener = [
            'name'     => $eventName,
            'closure'  => $callable,
            'priority' => $priority !== null ? $priority : self::PRIORITY_MEDIUM
        ];

        if (array_key_exists($eventName, $this->listeners)) {
            $this->listeners[$eventName][] = $listener;

            return;
        }

        $this->listeners[$eventName] = [$listener];
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch()
    {
        // Clear the list of raised events.
        $raisedEvents = $this->raisedEvents;
        $this->raisedEvents = [];

        foreach ($raisedEvents as $event) {
            // Get the listeners for this event
            $listeners = $this->getListeners(get_class($event));

            // Sort the listeners
            $sortedListeners = $this->sortListeners($listeners);

            // Call the listeners
            foreach ($sortedListeners as $listenerData) {
                if ($event->isPropagationStopped()) {
                    return;
                }

                $this->invoker->call($listenerData['closure'], [$event]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearEvents()
    {
        $this->raisedEvents = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getRaisedEvents()
    {
        return $this->raisedEvents;
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        if ($eventName == null) {
            return $this->listeners;
        }

        return array_key_exists($eventName, $this->listeners) ? $this->listeners[$eventName] : [];
    }

    /**
     * Sorts the listeners by priority.
     *
     * @param array $listeners
     *
     * @return array
     */
    private function sortListeners($listeners)
    {
        return array_sort_by($listeners, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });
    }
}