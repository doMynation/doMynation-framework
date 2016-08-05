<?php

namespace Domynation\Authentication;

/**
 * Interface UserInterface
 *
 * @package Domynation\Authentication
 */
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
     * @return \Domynation\Authorization\PermissionInterface[]
     */
    public function getPermissions();

    /**
     * @return bool
     */
    public function isAuthenticated();
}
