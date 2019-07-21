<?php

namespace Domynation\Bus;

/**
 * A command that can be cached.
 *
 * @package Domynation\Bus
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface CachableCommand
{
    /**
     * Returns the cache entry name.
     *
     * @return string
     */
    public function getCacheName();
}