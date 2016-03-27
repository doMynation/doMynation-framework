<?php

namespace Domynation\Security\Authentication;

interface UserAuthenticatorInterface
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
     * Authenticates as the user corresponding to the
     * id provided.
     *
     * @param $userId
     *
     * @return \Solarius\Common\Entities\User
     */
    public function authenticate($userId);

    /**
     * Deauthenticates the authenticated user.
     *
     * @return bool
     */
    public function deauthenticate();

    /**
     * Loads the currently authenticated user.
     *
     * @return User
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
     * @return User
     */
    public function getUser();
}