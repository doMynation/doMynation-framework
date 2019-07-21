<?php

namespace Domynation\Cache;

/**
 * Class InMemoryCache
 *
 * @package Domynation\Cache
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class InMemoryCache implements CacheInterface
{

    /**
     * @var array
     */
    private $store;

    /**
     * @var string
     */
    private $prefix;

    /**
     * InMemoryCache constructor.
     */
    public function __construct()
    {
        $this->store  = [];
        $this->prefix = "";
    }

    /**
     * Retrieves an item from the cache.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return array_key_exists($this->prefix . $key, $this->store) ? $this->store[$this->prefix . $key] : null;
    }

    /**
     * Retrieves many item from the cache.
     *
     * @param array $keys
     *
     * @return mixed
     */
    public function getMany(array $keys) : array
    {
        $values = [];

        foreach ($keys as $key) {
            if ($this->exists($key)) {
                $values[] = $this->get($key);
            }
        }

        return $values;
    }

    /**
     * Retrieve an item from the cache and delete it.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function pull(string $key)
    {
        $value = $this->get($key);

        $this->delete($key);

        return $value;
    }

    /**
     * Inserts an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param int $minutes
     *
     * @return mixed
     */
    public function set(string $key, $value, int $minutes)
    {
        $this->store[$this->prefix . $key] = $value;
    }

    /**
     * Inserts many items in the cache.
     *
     * @param array $values
     * @param int $minutes
     *
     * @return mixed
     */
    public function setMany(array $values, int $minutes)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $minutes);
        }
    }

    /**
     * Increments an item in the cache.
     *
     * @param string $key
     * @param int $amount
     *
     * @return mixed
     */
    public function increment(string $key, int $amount = 1)
    {
        if ($this->exists($key)) {
            $this->store[$this->prefix . $key] += $amount;
        }
    }

    /**
     * Decrements an item in the cache.
     *
     * @param string $key
     * @param int $amount
     *
     * @return mixed
     */
    public function decrement(string $key, int $amount = 1)
    {
        if ($this->exists($key)) {
            $this->store[$this->prefix . $key] -= $amount;
        }
    }

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key) : bool
    {
        // Since isset is language construct, it is much faster than a function call to array_key_exists(), but will return false if the value is null.
        // So we're first trying with isset, then with array_key_exists in case we had a null value.
        return isset($this->store[$this->prefix . $key]) || array_key_exists($this->prefix . $key, $this->store);
    }

    /**
     * Deletes an item in the cache.
     *
     * @param string $key
     *
     * @return int
     */
    public function delete(string $key) : int
    {
        if ($this->exists($key)) {
            unset($this->store[$this->prefix . $key]);

            return 1;
        }

        return 0;
    }

    /**
     * Deletes all item in the cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->store = [];
    }

    /**
     * Returns the prefix for all items in the cache.
     *
     * @return string
     */
    public function getPrefix() : string
    {
        return $this->prefix;
    }

    /**
     * Sets the prefix for all items in the cache.
     *
     * @param string $prefix
     *
     * @return mixed
     */
    public function setPrefix(string $prefix)
    {
        $this->prefix = $prefix;
    }
}