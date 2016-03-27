<?php

namespace Domynation\Contracts;

interface Arrayable
{
    /**
     * Returns an array representation of the data.
     *
     * @return array
     */
    public function toArray();
}