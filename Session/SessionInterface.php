<?php

namespace Domynation\Session;

/**
 * Interface SessionInterface
 *
 * @package Domynation\Session
 */
interface SessionInterface
{

    /**
     * Returns an item.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Checks if an item exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key);

    /**
     * Sets an item.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value);

    /**
     * Removes an item.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function remove($key);

    /**
     * Starts the session.
     */
    public function start();

    /**
     * Checks if the session is started.
     *
     * @return bool
     */
    public function isStarted();

    /**
     * Returns the session ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId($id);
}