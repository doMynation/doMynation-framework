<?php

/**
 * Generates a links to the given uri.
 *
 * @param string $uri
 * @param string $lang
 *
 * @return string
 */
function href($uri, $lang = null)
{
    $lang = $lang ? $lang : \Language::lang();

    return BASEURL . $lang . '/' . $uri;
}

/**
 * Redirects the user to the given location.
 *
 * @param string $uri
 */
function redirect($uri)
{
    header('Location: ' . href($uri));
}

/**
 * Redirects the user to the forbidden page.
 */
function forbidden()
{
    redirect('/403');
    exit;
}

/**
 * Decodes the inputs into ISO-8859-1
 *
 * @param $inputs
 *
 * @return array
 */
function utf8_decode_deep($inputs)
{
    array_walk_recursive($inputs, function (&$item, $key) {
        if (mb_detect_encoding($item, 'utf-8', true)) {
            $item = utf8_decode($item);
        }
    });

    return $inputs;
}

/**
 * Sends a response as json with a status code.
 *
 * @param array $data
 * @param int $status
 */
function respondJson($data = [], $status = HTTP_OK)
{
    $encoded  = utf8_encode_array($data);
    $response = new Symfony\Component\HttpFoundation\JsonResponse($encoded, $status);
    $response->setEncodingOptions(JSON_NUMERIC_CHECK);

    $response->send();
}

/**
 * Encodes each elements of an array to UTF-8.
 *
 * @param array $array
 *
 * @return array
 */
function utf8_encode_array(array $array)
{
    $encoded = [];

    foreach ($array as $key => $value) {
        $encoded[$key] = is_array($value) ? utf8_encode_array($value) : utf8_encode($value);
    }

    return $encoded;
}

/**
 * Converts each elements of an array to UTF-8.
 *
 * @param array $array
 *
 * @return array
 */
function utf8_decode_array(array $array)
{
    $decoded = [];

    foreach ($array as $key => $value) {
        $decoded[$key] = is_array($value) ? utf8_decode_array($value) : utf8_decode($value);
    }

    return $decoded;
}

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

