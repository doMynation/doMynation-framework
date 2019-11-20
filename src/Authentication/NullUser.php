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
    public function getId(): int
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName(): string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
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
    public function hasPermission($code): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuperUser(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimeZone(): string
    {
        return date_default_timezone_get();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return locale_get_default();
    }
}