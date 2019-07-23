<?php

namespace Domynation\Contracts;

/**
 * Interface Arrayable
 *
 * @package Domynation\Contracts
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface Arrayable
{
    /**
     * Returns an array representation of the data.
     *
     * @return array
     */
    public function toArray();
}