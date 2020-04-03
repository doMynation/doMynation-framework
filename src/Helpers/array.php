<?php

/**
 * Returns true if at least one value in the array satisfy the predicate.
 *
 * @param array $array
 * @param callable $predicate
 *
 * @return bool
 */
function array_some(array $array, callable $predicate)
{
    foreach ($array as $value) {
        if ($predicate($value)) {
            return true;
        }
    }

    return false;
}

/**
 * Returns true if every value in the array satisfy the predicate.
 *
 * @param array $array
 * @param callable $predicate
 *
 * @return bool
 */
function array_every(array $array, callable $predicate)
{
    foreach ($array as $value) {
        if (!$predicate($value)) {
            return false;
        }
    }

    return true;
}

/**
 * Partitions the array in two arrays based on a predicate.
 *
 * @param array $array
 * @param callable $predicate
 *
 * @return mixed
 * @internal param callable $closure
 *
 */
function array_partition(array $array, callable $predicate)
{
    return array_reduce($array, function ($acc, $value) use ($predicate) {
        if ($predicate($value)) {
            $acc[0][] = $value;
        } else {
            $acc[1][] = $value;
        }

        return $acc;
    }, [[], []]);
}

/**
 * Transforms a nested hierarchy into one array of depth 1.
 *
 * @param array $array
 *
 * @return array
 */
function array_flatten(array $array)
{
    return array_reduce($array, function ($acc, $a) {
        return is_array($a) ? array_merge($acc, array_flatten($a)) : array_merge($acc, [$a]);
    }, []);
}

/**
 * @param array $array
 * @param callable $closure
 *
 * @return array
 */
function array_flat_map(array $array, callable $closure)
{
    return array_flatten(array_map($closure, $array));
}

/**
 * @param array $array
 * @param callable $predicate
 * @param null $default
 *
 * @return mixed
 * @internal param callable $callback
 */
function array_first(array $array, callable $predicate, $default = null)
{
    foreach ($array as $key => $value) {
        if (call_user_func($predicate, $key, $value)) {
            return $value;
        }
    }

    return $default;
}


/**
 * @param array $array
 * @param callable $predicate
 *
 * @return bool|int|string
 */
function array_find(array $array, callable $predicate)
{
    foreach ($array as $key => $value) {
        if (call_user_func($predicate, $key, $value)) {
            return $key;
        }
    }

    return false;
}

/**
 * @param array $array
 * @param $key
 * @param $value
 *
 * @return bool|int|string
 */
function array_find_key(array $array, $key, $value)
{
    return array_find($array, function ($index, $element) use ($key, $value) {
        return $element[$key] == $value;
    });
}

/**
 * @param array $array
 * @param bool $preserveKeys
 *
 * @return array
 */
function array_sort(array $array, bool $preserveKeys = false)
{
    if ($preserveKeys) {
        asort($array);
    } else {
        sort($array);
    }

    return $array;
}

/**
 * @param array $array
 * @param callable $compare
 * @param bool $preserveKeys
 *
 * @return array
 */
function array_sort_by(array $array, callable $compare, bool $preserveKeys = false)
{
    if ($preserveKeys) {
        uasort($array, $compare);
    } else {
        usort($array, $compare);
    }

    return $array;
}

/**
 * @param array $array
 *
 * @return array
 */
function array_key_sort(array $array)
{
    ksort($array);

    return $array;
}

/**
 * @param array $array
 * @param callable $compare
 *
 * @return array
 */
function array_key_sort_by(array $array, callable $compare)
{
    uksort($array, $compare);

    return $array;
}

/**
 * @param array $array
 * @param callable $closure
 *
 * @return array
 */
function array_group_by(array $array, callable $closure)
{
    return array_reduce($array, function ($acc, $value) use ($closure) {
        $key = $closure($value);

        $acc[$key] = array_key_exists($key, $acc) ? array_merge($acc[$key], [$value]) : [$value];

        return $acc;
    }, []);
}

/**
 * Improved version of `array_reduce` that supports passing the key in addition to the value.
 *
 * @param array $array
 * @param callable $closure
 * @param mixed $initial
 *
 * @return mixed
 */
function array_fold(array $array, callable $closure, $initial = [])
{
    $accumulator = $initial;

    foreach ($array as $key => $value) {
        $accumulator = $closure($accumulator, $key, $value);
    }

    return $accumulator;
}
