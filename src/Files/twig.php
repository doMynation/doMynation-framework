<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

/** @var \Twig_Environment $twig */
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

/*
|--------------------------------------------------------------------------
| Internationalization (i18n)
|--------------------------------------------------------------------------
*/
/** @var \Domynation\I18N\Translator $translator */
/** @var \Symfony\Component\HttpFoundation\Request $request */

$twig->addGlobal('currentLocale', $translator->getLocale());
$twig->addFilter('tr', function (string $key, array $placeholders = [], string $preferredLocale = null) use ($translator) {
    return $translator->trans($key, $placeholders, $preferredLocale);
});
