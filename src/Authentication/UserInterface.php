<?php

namespace Domynation\Authentication;

interface UserInterface
{

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getFullName();

    /**
     * @return string
     */
    public function getUsername();

    /**
     * @param string|string[] $code
     *
     * @return bool
     */
    public function hasPermission($code);

    /**
     * @return array
     */
    public function getPermissions();

    /**
     * @return bool
     */
    public function isAuthenticated();
}
