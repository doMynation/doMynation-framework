<?php

namespace Domynation\Cache;

use Predis\Client;

/**
 * Class RedisCache
 *
 * @package Domynation\Cache
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class RedisCache implements CacheInterface
{

    /**
     * @var \Predis\Client
     */
    private $predis;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(string $host, string $port)
    {
        $this->predis = new Client([
            'scheme' => 'tcp',
            'host'   => $host,
            'port'   => $port
        ]);

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
        if (!is_null($value = $this->predis->get($this->prefix . $key))) {
            return is_numeric($value) ? $value : unserialize($value);
        }
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
        // Retrieve all keys
        $items = $this->predis->mget(array_map(function ($key) {
            return $this->prefix . $key;
        }, $keys));

        // Unserialize items
        return array_map(function ($item) {
            return is_numeric($item) ? $item : unserialize($item);
        }, $items);
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
        // Retrieve
        $value = $this->get($key);

        // Delete
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
        $value = is_numeric($value) ? $value : serialize($value);

        $this->predis->setex($this->prefix . $key, 60 * $minutes, $value);
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
        $this->predis->pipeline(function ($pipe) use ($values, $minutes) {
            foreach ($values as $key => $value) {
                $value = is_numeric($value) ? $value : serialize($value);

                $pipe->setex($this->prefix . $key, 60 * $minutes, $value);
            }
        });
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
        $this->predis->incrby($this->prefix . $key, $amount);
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
        $this->predis->decrby($this->prefix . $key, $amount);
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
        return $this->predis->del([$this->prefix . $key]);
    }

    /**
     * Deletes all item in the cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->predis->flushdb();
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

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists(string $key) : bool
    {
        return (bool)$this->predis->exists($this->prefix . $key);
    }
}