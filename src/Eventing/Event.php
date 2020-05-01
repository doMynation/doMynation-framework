<?php

declare(strict_types=1);

namespace Domynation\Eventing;

/**
 * Class Event
 *
 * @package Domynation\Eventing
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class Event
{
    private bool $propagationStopped = false;

    /**
     * @return boolean
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the progapation of this event.
     *
     * @return void
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}