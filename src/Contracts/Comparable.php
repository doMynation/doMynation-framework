<?php

namespace Domynation\Contracts;

/**
 * Interface Comparable
 *
 * @package Domynation\Contracts
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface Comparable
{
    /**
     * Compares the object to another Comparable.
     *
     * @param $other
     *
     * @return int
     */
    public function compareTo($other);

    /**
     * Checks if the object is equal to an other object.
     *
     * @param $other
     *
     * @return boolean
     */
    public function equals($other);
}