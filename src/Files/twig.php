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
        // This filter is variadic on the last argument, so whatever is supplied as the placeholder will be wrapped in an array (e.g. 'banana' => ['banana']).
        // When the first placeholder is an array, it means the view passed an associative array with named placeholders, in which case
        // said array will be stored in $placeholder[0].
        $placeholders = is_array($placeholders[0] ?? null) ? $placeholders[0] : $placeholders;

        return $translator->trans($key, $placeholders);
    }, [
        'is_safe'     => ['html'],
        'is_variadic' => true,
    ])
);

$twig->addFunction(
    new TwigFunction('dumpTranslations', function () use ($translator) {
        $translations = $translator->getAllTranslations();

        return json_encode($translations);
    })
);