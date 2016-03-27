<?php

namespace Domynation\Security;

/**
 * This class is totally insecure and is only there to support the very old authentication mechanism.
 *
 * @package Domynation\Security
 */
final class Md5Password implements PasswordInterface
{

    /**
     * Checks if a password matches a hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function check($password, $hash)
    {
        return md5($password) === $hash;
    }

    /**
     * Hashes the password.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash($password)
    {
        return md5($password);
    }

    /**
     * Checks if a password needs to be replaced with a new hash.
     *
     * @param string $password
     *
     * @return bool
     */
    public function needsRehash($password)
    {
        return preg_match('/^[a-f0-9]{32}$/', $password);
    }
}