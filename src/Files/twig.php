<?php

declare(strict_types=1);

use Twig\TwigFilter;
use Twig\TwigFunction;

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/** @var \Twig_Environment $twig */

$twig->addFunction(new TwigFunction('dump', function ($value) {
    return dump($value);
}));

$twig->addFunction(new TwigFunction('call', function ($function, $value) {
    return $function($value);
}));

$twig->addFunction(new TwigFunction('ceil', function ($value) {
    return ceil($value);
}));

$twig->addFunction(new TwigFunction('instanceOf', function ($thing, $class) {
    return $thing instanceof $class;
}));

/*
|--------------------------------------------------------------------------
| Internationalization (i18n)
|--------------------------------------------------------------------------
*/
/** @var \Domynation\I18N\Translator $translator */

$twig->addGlobal('currentLocale', $translator->getLocale());
$twig->addFilter(
    new TwigFilter('tr', function (string $key, array $placeholders = []) use ($translator) {
        return $translator->trans($key, $placeholders);
    }, [
        'is_safe'     => ['html'],
        'is_variadic' => true,
    ])
);
