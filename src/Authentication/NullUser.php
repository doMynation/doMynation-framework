<?php

namespace Domynation\Authentication;

final class NullUser implements UserInterface
{

    /**
     * @return int
     */
    public function getId()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return null;
    }

    /**
     * @param string|string[] $code
     *
     * @return bool
     */
    public function hasPermission($code)
    {
        return false;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return false;
    }
}