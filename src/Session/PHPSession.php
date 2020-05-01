<?php

declare(strict_types=1);

namespace Domynation\Session;

use RuntimeException;

/**
 * Class PHPSession
 *
 * @package Domynation\Session
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class PHPSession implements SessionInterface
{
    private bool $isStarted = false;

    /**
     * {@inheritdoc}
     */
    public function start(): void
    {
        if ($this->isStarted) {
            return;
        }

        if (!session_start()) {
            throw new RuntimeException("Failed to start a session");
        }

        $this->isStarted = true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ?? array_key_exists($key, $_SESSION) ? $_SESSION[$key] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $return = null;

        if (array_key_exists($key, $_SESSION)) {
            $return = $_SESSION[$key];
            unset($_SESSION[$key]);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key): bool
    {
        return isset($_SESSION[$key]) || array_key_exists($key, $_SESSION);
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted(): bool
    {
        return $this->isStarted;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return session_id();
    }

    /**
     * {@inheritdoc}
     */
    public function setId(string $id): void
    {
        if (!$this->isStarted) {
            throw new RuntimeException("Cannot set the ID if the session is not started.");
        }

        session_id($id);
    }

    /**
     * {@inheritdoc}
     */
    public function close(): void
    {
        session_write_close();
    }
}