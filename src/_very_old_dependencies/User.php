<?php

/**
 * @todo: Replace this extremely old class by an appropriate interface.
 */
final class User
{

    private static $info = [];
    private static $isLogged = false;

    /**
     * @return int
     */
    public static function id()
    {
        return static::$info['id'];
    }

    /**
     * Initializes the User with data.
     *
     * @param array $data
     */
    public static function init(array $data)
    {
        static::$info     = $data;
        static::$isLogged = true;
    }

    /**
     * @param $index
     *
     * @return mixed
     */
    public static function get($index)
    {
        return array_key_exists($index, static::$info) ? static::$info[$index] : null;
    }

    /**
     * Checks if the user is logged in
     *
     * @return bool
     */
    public static function isLogged()
    {
        return static::$isLogged;
    }

    /**
     * Checks if the user possesses the provided permissions.
     *
     * @param string|string[] $permissionCodes
     *
     * @return bool
     */
    public static function hasAccess($permissionCodes)
    {
        if (!is_array($permissionCodes)) {
            return in_array($permissionCodes, static::$info['permissions']);
        }

        return array_every($permissionCodes, function ($code) {
            return in_array($code, static::$info['permissions']);
        });
    }
}
