<?php

namespace Domynation\Authentication;

/**
 * Interface for the authentication logic.
 *
 * @package Domynation\Authentication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface AuthenticatorInterface
{

    /**
     * Attempts to authenticate a user.
     *
     * @param string $username
     * @param string $password
     *
     * @return int|null The user id or `null` if the authentication failed
     */
    public function attempt(string $username, string $password): ?int;

    /**
     * Authenticates as the user corresponding to the id provided.
     *
     * @param int $userId
     */
    public function authenticate(int $userId): void;

    /**
     * Deauthenticates the user.
     */
    public function deauthenticate(): void;

    /**
     * Remembers the currently authenticated user.
     *
     * @return int|null
     */
    public function remember(): ?int;

    /**
     * Checks if the user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * Returns the ID of the authenticated user. Returns `null` if the user isn't authenticated.
     *
     * @return int|null
     */
    public function getUserId(): ?int;
}