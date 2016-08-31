<?php

namespace Domynation\Cache;

/**
 * Interface CacheInterface
 *
 * @package Domynation\Cache
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface CacheInterface
{

    /**
     * Retrieves an item from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * Retrieves many item from the cache.
     *
     * @param array $keys
     *
     * @return mixed
     */
    public function getMany(array $keys) : array;

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function pull(string $key);

    /**
     * Inserts an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $minutes
     *
     * @return mixed
     */
    public function set(string $key, $value, int $minutes);

    /**
     * Inserts many items in the cache.
     *
     * @param array $values
     * @param int $minutes
     *
     * @return mixed
     */
    public function setMany(array $values, int $minutes);

    /**
     * Increments an item in the cache.
     *
     * @param string $key
     * @param int $amount
     *
     * @return mixed
     */
    public function increment(string $key, int $amount = 1);

    /**
     * Decrements an item in the cache.
     *
     * @param string $key
     * @param int $amount
     *
     * @return mixed
     */
    public function decrement(string $key, int $amount = 1);

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key) : bool;

    /**
     * Deletes an item in the cache.
     *
     * @param string $key
     *
     * @return int
     */
    public function delete(string $key) : int;

    /**
     * Deletes all item in the cache.
     *
     * @return void
     */
    public function flush();

    /**
     * Returns the prefix for all items in the cache.
     *
     * @return string
     */
    public function getPrefix() : string;

    /**
     * Sets the prefix for all items in the cache.
     *
     * @param string $prefix
     *
     * @return mixed
     */
    public function setPrefix(string $prefix);
}