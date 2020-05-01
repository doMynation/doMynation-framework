<?php

declare(strict_types=1);

namespace Domynation\Security;

/**
 * Interface PasswordInterface
 *
 * @package Domynation\Security
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface PasswordInterface
{

    /**
     * Checks if a password matches a hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function check($password, $hash): bool;

    /**
     * Hashes the password.
     *
     * @param string $password
     *
     * @return string
     */
    public function hash($password): string;

    /**
     * Checks if a password needs to be replaced with a new hash.
     *
     * @param string $password
     *
     * @return bool
     */
    public function needsRehash($password): bool;
}