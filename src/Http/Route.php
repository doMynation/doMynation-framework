<?php

namespace Domynation\Http;

final class Route
{

    /**
     * @var string
     */
    private $type;

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

    public function __construct($name, \Closure $handler)
    {
        $this->type                = "GET"; // @todo implement this
        $this->name                = $name;
        $this->handler             = $handler;
        $this->validator           = null;
        $this->isSecure            = true;
        $this->requiredPermissions = [];
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
     * Gets the value of type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
}