<?php

namespace Domynation\Eventing;

abstract class Event
{
    /**
     * @var bool
     */
    private $propagationStopped = false;

    /**
     * @return boolean
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the progapation of this event.
     *
     * @return void
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }
}