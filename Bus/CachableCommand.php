<?php

namespace Domynation\Bus;

interface CachableCommand
{
    /**
     * Gets the cache entry name.
     *
     * @return string
     */
    public function getCacheName();
}