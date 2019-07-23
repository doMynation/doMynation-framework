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
     * @return int|bool The user id or FALSE if the authentication failed
     */
    public function attempt($username, $password);

    /**
     * Authenticates as the user corresponding to the id provided.
     *
     * @param int $userId
     *
     * @return UserInterface
     */
    public function authenticate($userId);

    /**
     * Deauthenticates the user.
     *
     * @return bool
     */
    public function deauthenticate();

    /**
     * Remembers the currently authenticated user.
     *
     * @return UserInterface
     */
    public function remember();

    /**
     * Checks if the user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated();

    /**
     * Returns the authenticated user.
     *
     * @return UserInterface
     */
    public function getUser();
}