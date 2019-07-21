<?php

/***********************************************************
 *
 * GLOBALS
 */

$twig->addGlobal('currentLang', \Language::lang());

/***********************************************************
 * FUNCTIONS
 */

$twig->addFunction(new Twig_SimpleFunction('dump', function ($value) {
    return dump($value);
}));

$twig->addFunction(new Twig_SimpleFunction('call', function ($function, $value) {
    return $function($value);
}));

$twig->addFunction(new Twig_SimpleFunction('ceil', function ($value) {
    return ceil($value);
}));

$twig->addFunction(new Twig_SimpleFunction('now', function ($showTime = true) {
    return formatDate(new \DateTime, $showTime);
}));

$twig->addFunction(new Twig_SimpleFunction('instanceOf', function ($thing, $class) {
    return $thing instanceof $class;
}));

$twig->addFunction(new Twig_SimpleFunction('getLang', function () {
    return \Language::lang();
}));

/***********************************************************
 * FILTERS
 */

$twig->addFilter(new Twig_SimpleFilter('translate', function ($key, $placeholders = []) {
    return lang($key, $placeholders);
}));

// Aliases for convenience
$twig->addFilter(new Twig_SimpleFilter('trans', function ($key, $placeholders = []) {
    return lang($key, $placeholders);
}));

$twig->addFilter(new Twig_SimpleFilter('tr', function ($key, $placeholders = []) {
    return lang($key, $placeholders);
}));

$twig->addFilter(new Twig_SimpleFilter('escapeInput', function ($value) {
    return htmlspecialchars($value);
}));

$twig->addFilter(new Twig_SimpleFilter('dateHr', function ($date, $showTime = true) {
    return formatDate($date, $showTime);
}));
