<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
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

$twig->addFunction(new Twig_SimpleFunction('instanceOf', function ($thing, $class) {
    return $thing instanceof $class;
}));