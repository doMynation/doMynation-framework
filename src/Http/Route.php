<?php

declare(strict_types=1);

namespace Domynation\Http;

/**
 * The description of a route.
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Route
{
    private string $method;
    private string $name;

    /**
     * @var string|\Closure
     */
    private $handler;

    /**
     * @var callable|string
     */
    private $validator;
    private array $requiredPermissions;

    /**
     * Whether or not this route requires the user to be authenticated.
     *
     * @var bool
     */
    private bool $isSecure;

    /**
     * Indicates whether the request is read-only.
     *
     * @var bool
     */
    private bool $isReadOnly;

    /**
     * Route constructor.
     *
     * @param string $name
     * @param string|callable $handler
     * @param string $method
     */
    public function __construct(string $name, $handler, string $method = 'GET')
    {
        $this->method = strtoupper($method);
        $this->name = $name;
        $this->handler = $handler;
        $this->validator = null;
        $this->isSecure = true;
        $this->requiredPermissions = [];
        $this->isReadOnly = false;
    }

    /**
     * Defines the required permissions to access this route.
     *
     * @param string|array $permission
     *
     * @return $this
     */
    public function setPermission($permission)
    {
        // Always work with an array
        $newPermissions = is_array($permission) ? $permission : [$permission];

        // Add the permission(s) to the required permissions
        $this->requiredPermissions = array_unique(array_merge($this->requiredPermissions, $newPermissions));

        return $this;
    }

    /**
     * Sets the validator class for this route.
     *
     * @param callable|string $validator
     *
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Allows guest user to access this route.
     *
     * @return $this
     */
    public function allowGuest()
    {
        $this->isSecure = false;

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function hasValidator(): bool
    {
        return $this->validator !== null;
    }

    /**
     * @return callable|string
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return callable|string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    public function getRequiredPermissions(): array
    {
        return $this->requiredPermissions;
    }

    /**
     * Returns true if the route requires authentication.
     *
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->isSecure;
    }

    /**
     * @return boolean
     */
    public function isReadOnly(): bool
    {
        return $this->isReadOnly;
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return $this
     */
    public function readOnly(): self
    {
        $this->isReadOnly = true;

        return $this;
    }
}