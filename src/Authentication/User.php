<?php

namespace Domynation\Authentication;

use Domynation\Authorization\PermissionInterface;
use Domynation\Entities\Entity;

class User extends Entity implements UserInterface
{

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var PermissionInterface[]
     */
    protected $permissions;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var \DateTime
     */
    protected $passwordExpiresAt;

    public function __construct($id, $username, $fullName)
    {
        $this->id       = $id;
        $this->username = $username;
        $this->fullName = $fullName;
    }

    public function toArray()
    {
        return [];
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of fullName.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Gets the value of email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password of the user.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Sets the password expiry date.
     *
     * @param \DateTime $passwordExpiresAt
     */
    public function setPasswordExpiresAt(\DateTime $passwordExpiresAt)
    {
        $this->passwordExpiresAt = $passwordExpiresAt;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordExpiresAt()
    {
        return $this->passwordExpiresAt;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $code
     *
     * @return bool
     */
    public function hasPermission($code)
    {
        return array_some($this->permissions, function (PermissionInterface $permission) use ($code) {
            return $permission->getCode() === $code;
        });
    }
}