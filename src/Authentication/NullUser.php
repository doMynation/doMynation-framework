<?php

namespace Domynation\Authentication;

/**
 * A non-authenticated user.
 *
 * @package Domynation\Authentication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class NullUser implements UserInterface
{

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($code)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return false;
    }
}