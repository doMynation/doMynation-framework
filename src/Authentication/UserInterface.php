<?php

declare(strict_types=1);

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
    public function getId(): ?int;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getUsername(): string;

    /**
     * @return string
     */
    public function getPassword(): string;

    /**
     * @param string|string[] $code
     *
     * @return bool
     */
    public function hasPermission($code): bool;

    /**
     * @return array
     */
    public function getPermissions(): array;

    /**
     * @return bool
     */
    public function isAuthenticated(): bool;

    /**
     * @return bool
     */
    public function isSuperUser(): bool;

    /**
     * @return string
     */
    public function getTimeZone(): string;

    /**
     * @return string
     */
    public function getLocale(): string;
}
