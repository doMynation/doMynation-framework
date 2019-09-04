<?php

/**
 * @param $line
 * @param array $placeholders
 *
 * @return mixed
 */
function lang($line, $placeholders = [])
{
    $line = \Language::get($line);

    foreach ($placeholders as $key => $value) {
        $line = str_replace("{" . $key . "}", $value, $line);
    }

    return $line;
}

/**
 * Formats a date according to the locale.
 *
 * @param \DateTime $date
 * @param bool $showTime
 *
 * @return string
 */
function formatDate(DateTime $date, $showTime = true)
{
    $carbon = Carbon\Carbon::instance($date);

    $format = $showTime ? "%d %B %Y à %Hh%M" : "%d %B %Y";
    if (\Language::lang() === 'fr') {
        return $carbon->formatLocalized($format);
    }

    $format = $showTime ? "%B %d, %Y at %H:%M" : "%B %d, %Y";

    return $carbon->formatLocalized($format);
}

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
