<?php

namespace Domynation\Entities;

trait TimestampableTrait
{

    /**
     * Updates the timestamp at which an entity was last udpated.
     */
    protected function touch()
    {
        $this->updatedAt = new \DateTime;
    }
}