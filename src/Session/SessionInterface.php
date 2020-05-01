<?php

declare(strict_types=1);

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
    public function has($key): bool;

    /**
     * Sets an item.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function set($key, $value): void;

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
    public function start(): void;

    /**
     * Checks if the session is started.
     *
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * Returns the session ID.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Sets the session ID.
     *
     * @param string $id
     */
    public function setId(string $id): void;

    /**
     * Closes the session.
     */
    public function close(): void;
}