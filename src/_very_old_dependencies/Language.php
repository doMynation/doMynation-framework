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
    private static $basePath;

    public static function initialize($language, $basePath)
    {
        // Store in cookies
        $_COOKIE['lang'] = $language;
        setcookie('lang', $language, null, '/');

        static::$basePath = $basePath;
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

        if (file_exists(static::$basePath . '/config/languages/' . $fileName)) {
            static::$language = require_once static::$basePath . '/config/languages/' . $fileName;
        }
    }

    public static function get($line)
    {
        return isset(static::$language[$line]) ? static::$language[$line] : $line;
    }
}
