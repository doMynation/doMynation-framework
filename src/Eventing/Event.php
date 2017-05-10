<?php

namespace Domynation\Eventing;

/**
 * Class Event
 *
 * @package Domynation\Eventing
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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