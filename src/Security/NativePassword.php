<?php

namespace Domynation\Security;

/**
 * Class NativePassword
 *
 * @package Domynation\Security
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NativePassword implements PasswordInterface
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
        return password_verify($password, $hash);
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
        return password_hash($password, PASSWORD_DEFAULT, [
            'cost' => 13
        ]);
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
        return password_needs_rehash($password, PASSWORD_DEFAULT);
    }
}