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
