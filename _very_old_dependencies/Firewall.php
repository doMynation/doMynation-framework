<?php

/**
 * @todo: Extremely old code. Refactor this global singleton and replace it with an injectable interface.
 */
final class Firewall
{

    /**
     * @var array
     */
    static $rules = [];

    /**
     * @param string $name
     * @param callable $closure
     */
    public static function addRule($name, $closure)
    {
        static::$rules[$name] = $closure;
    }

    /**
     * @param array $params
     */
    public static function check($params = [])
    {
        // Go through all rules in order and check them
        foreach (static::$rules as $rule) {
            $rule($params);
        }
    }
}
