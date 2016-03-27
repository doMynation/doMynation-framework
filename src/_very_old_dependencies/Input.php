<?php

/**
 * Class responsible storing and decoding request inputs.
 *
 * @todo: Refactor/move this uglyness into a Request class or use Symfony'
 */
final class Input
{

    static $inputs;

    public static function initialize()
    {
        if (IS_AJAX) {
            static::$inputs['get']  = !empty($_GET) ? utf8_decode_array($_GET) : [];
            static::$inputs['post'] = !empty($_POST) ? utf8_decode_array($_POST) : [];
        } else {
            static::$inputs['get']  = !empty($_GET) ? $_GET : [];
            static::$inputs['post'] = !empty($_POST) ? $_POST : [];
        }
    }

    public static function all($type = 'get')
    {
        return static::$inputs[$type];
    }

    public static function get($index)
    {
        return array_key_exists($index, static::$inputs['get']) ? static::$inputs['get'][$index] : null;
    }

    public static function post($index)
    {
        return array_key_exists($index, static::$inputs['post']) ? static::$inputs['post'][$index] : null;
    }

    public static function exists($index)
    {
        return array_key_exists($index, static::$inputs['post']) ? true : array_key_exists($index, static::$inputs['get']);
    }
}
