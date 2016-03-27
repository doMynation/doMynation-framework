<?php

/**
 * Class responsible for generating views and layouts
 */
final class View
{

    static $layouts = [];

    public static function make($viewName, $viewData = [])
    {
        // Buffer the output
        $view = static::buffer(PATH_HTML . $viewName . '.php', $viewData);

        return $view;
    }

    private static function buffer($file, $_d_data)
    {
        // Create variables
        $_d_vars = is_object($_d_data) ? get_object_vars($_d_data) : $_d_data;

        extract($_d_vars);

        // Load file
        if (is_file($file)) {
            ob_start();

            include $file;
            $_d_contents = ob_get_contents();

            ob_end_clean();

            return $_d_contents;
        }

        return false;
    }
}
