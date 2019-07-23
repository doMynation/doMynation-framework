<?php

namespace Domynation\Session;

/**
 * Class PHPSession
 *
 * @package Domynation\Session
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class PHPSession implements SessionInterface
{
    /**
     * @var bool
     */
    private $isStarted = false;

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->isStarted) {
            return;
        }

        if (!session_start()) {
            throw new \RuntimeException("Failed to start a session");
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
    public function set($key, $value)
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
    public function has($key)
    {
        return isset($_SESSION[$key]) || array_key_exists($key, $_SESSION);
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->isStarted;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        if (!$this->isStarted) {
            throw new \RuntimeException("Cannot set the ID if the session is not started.");
        }

        session_id($id);
    }
}