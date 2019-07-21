<?php

/**
 * @todo Extremely old code. Refactor this.
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Language
{

    private static $language = [];
    private static $lang;

    public static function initialize($language)
    {
        // Store in cookies
        $_COOKIE['lang'] = $language;
        setcookie('lang', $language, null, '/');

        static::$lang = $language;
        static::load($language);
    }

    public static function lang()
    {
        return static::$lang;
    }

    public static function load($langCode)
    {
        $fileName = $langCode . '.php';

        if (file_exists(PATH_BASE . '/config/languages/' . $fileName)) {
            static::$language = require_once PATH_BASE . '/config/languages/' . $fileName;
        } else {
            $fileName = DEFAULT_LANG . '.php';

            if (file_exists(PATH_BASE . '/config/languages/' . $fileName)) {
                static::$language = require_once PATH_BASE . '/config/languages/' . $fileName;
            } else {
                die('Unable to load the requested language file: ' . $fileName);
            }
        }
    }

    public static function get($line)
    {
        return isset(static::$language[$line]) ? static::$language[$line] : $line;
    }
}
