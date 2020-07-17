<?php

declare(strict_types=1);

/**
 * Returns a memoized version of the provided function. Memoized remmember the result
 * of previous calls by storing them in memory. This helps preventing the unnecessary execution
 * of expensive functions when the result is already known.
 *
 * @param callable $function
 *
 * @return callable
 */
function memoize(callable $function): callable
{
    return function () use ($function) {
        static $cache = [];

        $args = func_get_args();
        $key = serialize($args);

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $cache[$key] = call_user_func_array($function, $args);

        return $cache[$key];
    };
}
