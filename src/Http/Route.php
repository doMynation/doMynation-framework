<?php

namespace Domynation\Http;

/**
 * The description of a route.
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Route
{

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Closure
     */
    private $handler;

    /**
     * @var string
     */
    private $validator;

    /**
     * @var array
     */
    private $requiredPermissions;

    /**
     * Whether or not this route requires the user to be authenticated.
     *
     * @var bool
     */
    private $isSecure;

    /**
     * Indicates whether the request is read-only.
     *
     * @var bool
     */
    private $isReadOnly;

    /**
     * Route constructor.
     *
     * @param string name
     * @param callable $handler
     * @param string $method
     */
    public function __construct($name, callable $handler, $method = 'GET')
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
     * @param string $validator
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

    /**
     * Gets the value of method.
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Gets the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function hasValidator()
    {
        return !is_null($this->validator);
    }

    /**
     * Gets the value of validator.
     *
     * @return string
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Gets the value of handler.
     *
     * @return \Closure
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Gets the value of requiredPermissions.
     *
     * @return array
     */
    public function getRequiredPermissions()
    {
        return $this->requiredPermissions;
    }

    /**
     * Returns true if the route requires authentication.
     *
     * @return bool
     */
    public function isSecure()
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
     * @return \Domynation\Http\Route
     */
    public function readOnly(): self
    {
        $this->isReadOnly = true;

        return $this;
    }
}