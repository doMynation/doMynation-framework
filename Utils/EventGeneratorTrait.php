<?php

namespace Domynation\Utils;

trait EventGeneratorTrait
{

    /**
     * Raises an event.
     *
     * @param string $eventName
     * @param ...$args
     */
    public function raise($eventName, $args = null)
    {
        $args      = func_get_args();
        $eventArgs = array_splice($args, 1);

        \Event::fire($eventName, $eventArgs);
    }

    /**
     * Queues an event to be dispatched later.
     *
     * @param string $eventName
     * @param ...$args
     */
    public function queue($eventName, $args = null)
    {
        $args      = func_get_args();
        $eventArgs = array_splice($args, 1);

        \Event::raise($eventName, $eventArgs);
    }
}