<?php

use Domynation\Session\SessionInterface;

/**
 * @todo Extremely old code. Refactor all of this crap.
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Language
{

    private static $language = [];
    private static $lang;

    public static function initialize(SessionInterface $session, $inputLang = null)
    {
        $lang = $inputLang ? $inputLang : DEFAULT_LANG;

        if (!$session->has('lang') || $session->get('lang') != $lang) {
            $session->set('lang', $lang);
        }

        static::load($lang);
        static::$lang = $lang;
    }

    public static function lang()
    {
        return static::$lang;
    }

    public static function load($langCode)
    {
        $fileName = $langCode . '.php';

        if (file_exists(PATH_BASE . '/config/languages/' . $fileName)) {
            $lang = include_once(PATH_BASE . '/config/languages/' . $fileName);

            static::$language = array_merge(static::$language, $lang);

            static::$lang = $langCode;
        } else {
            $fileName = DEFAULT_LANG . '.php';

            if (file_exists(PATH_BASE . '/config/languages/' . $fileName)) {
                $lang = include_once(PATH_BASE . '/config/languages/' . $fileName);

                static::$language = array_merge(static::$language, $lang);

                static::$lang = DEFAULT_LANG;
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
