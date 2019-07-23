<?php

namespace Domynation\Utils;

/**
 * A data structure that automatically combines similar values and
 * counts occurrences.
 *
 * @todo: This is too specific and probably belongs to userland
 *
 * @package Domynation\Utils
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Combinator
{
    /**
     * @var array
     */
    private $values;

    /**
     * Combinator constructor.
     */
    public function __construct()
    {
        $this->values = [];
    }

    /**
     * Inserts an item in the combinator.
     *
     * @param mixed $key
     * @param int $quantity
     */
    public function insert($key, $quantity)
    {
        if (!is_int($quantity)) {
            throw new \InvalidArgumentException("Quantity must be numeric");
        }

        // Combine the quantity if it exists
        if ($this->exists($key)) {
            $this->values[$key] += $quantity;

            return;
        }

        // Otherwise insert the new value.
        $this->values[$key] = $quantity;
    }

    /**
     * Returns the quantity associated with the given key.
     *
     * @param mixed $key
     *
     * @return int
     */
    public function get($key)
    {
        if ($this->exists($key)) {
            return $this->values[$key];
        }
    }

    /**
     * Checks if a value already exists for the provided
     * key.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Returns the keys as an array.
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->values);
    }

    /**
     * Returns the elements as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->values;
    }
}