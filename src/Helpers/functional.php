<?php

/**
 * Returns a memoized version of the provided function. Memoized remmember the result
 * of previous calls by storing them in memory. This helps preventing the unnecessary execution
 * of expensive functions when the result is already known.
 *
 * @param callable $function
 *
 * @return callable
 */
function memoize(callable $function)
{
    return function () use ($function) {
        static $cache = [];

        $args = func_get_args();
        $key  = serialize($args);

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $cache[$key] = call_user_func_array($function, $args);

        return $cache[$key];
    };
}

/**
 * Returns a curried version of the array_filter function.
 *
 * @param callable $function
 *
 * @return callable
 */
function filter(callable $function)
{
    return function (array $array) use ($function) {
        return array_filter($array, $function);
    };
}

;
/**
 * Returns a curried version of array_map.
 *
 * @param callable $function
 *
 * @return callable
 */
function map(callable $function)
{
    return curry('array_map', $function);
}

/**
 * Transforms a function taking two arguments into a function
 * that takes one argument.
 *
 * @param callable $f
 * @param mixed $firstArg
 *
 * @return callable
 */
function curry(callable $f, $firstArg)
{
    return function (...$args) use ($f, $firstArg) {
        return call_user_func_array($f, array_merge([$firstArg], $args));
    };
}

/**
 * Composes multiple functions into one function.
 *
 * Ex: compose($f1, $f2, $f3) === $f1($f2($f3(args)))
 *
 * @param callable[] ...$functions
 *
 * @return callable
 */
function compose(callable ...$functions)
{
    return function ($i) use ($functions) {
        return array_reduce(array_reverse($functions), function ($acc, $func) {
            return $func($acc);
        }, $i);
    };
}

/**
 *
 * @param int $n
 * @param \Generator $gen
 *
 * @return \Generator
 */
function take($n, Generator $gen)
{
    while ($n-- > 0 && $gen->valid()) {
        yield $gen->current();
        $gen->next();
    }
}

