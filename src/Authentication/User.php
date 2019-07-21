<?php

namespace Domynation\Authentication;

use Domynation\Authorization\PermissionInterface;
use Domynation\Entities\Entity;

/**
 * A default user implementation.
 *
 * @package Domynation\Authentication
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
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

    /**
     * User constructor.
     *
     * @param $id
     * @param $username
     * @param $fullName
     */
    public function __construct($id, $username, $fullName)
    {
        $this->id       = $id;
        $this->username = $username;
        $this->fullName = $fullName;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'fullName' => $this->fullName,
        ];
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
     * Gets the value of password.
     *
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
     * {@inheritdoc}
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission($code)
    {
        // Collect all the codes
        $permissionCodes = array_map(function (PermissionInterface $permission) {
            return $permission->getCode();
        }, $this->permissions);

        if (!is_array($code)) {
            return in_array($code, $permissionCodes);
        }

        return array_every($code, function ($permissionCode) use ($permissionCodes) {
            return in_array($permissionCode, $permissionCodes);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthenticated()
    {
        return true;
    }
}