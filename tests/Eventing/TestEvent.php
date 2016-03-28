<?php

namespace Test\Eventing;

use Domynation\Eventing\Event;

final class TestEvent extends Event
{
    private $someData;

    public function __construct($someData)
    {
        $this->someData = $someData;
    }

    /**
     * @return mixed
     */
    public function getSomeData()
    {
        return $this->someData;
    }
}