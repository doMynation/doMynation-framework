<?php

namespace Domynation\Authentication;

/**
 * A user definition.
 *
 * @package Domynation\Authentication
 * @author Dominique Sarrazin <domynation@gmail.com>
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
     * @return string
     */
    public function getPassword(): string;

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
