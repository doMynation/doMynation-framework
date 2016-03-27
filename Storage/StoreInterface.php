<?php

namespace Domynation\Storage;

/**
 * Interface StoreInterface
 *
 * @package Domynation\Core
 */
interface StoreInterface
{

    /**
     * Retrieves an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * Retrieves an item from the store and deletes it.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function pull($key);

    /**
     * Fetches all the items in the store.
     *
     * @return array
     */
    public function getAll();

    /**
     * Sets an item in the store.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * Deletes an item from the store.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function delete($key);

    /**
     * Checks if an item exists in the store.
     *
     * @param string $key
     *
     * @return bool
     */
    public function exists($key);
}