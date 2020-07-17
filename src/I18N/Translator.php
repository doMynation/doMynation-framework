<?php

namespace Domynation\I18N;

use Symfony\Component\Translation\Translator as SymfonyTranslator;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Translator
{
    private string $locale;
    private array $supportedLocales;
    private SymfonyTranslator $innerTranslator;

    public function __construct(string $locale, array $supportedLocales, SymfonyTranslator $innerTranslator)
    {
        $this->locale = $locale;
        $this->supportedLocales = $supportedLocales;
        $this->innerTranslator = $innerTranslator;
    }

    /**
     * Translates a given key.
     *
     * @param string $key
     * @param array $placeholders
     * @param string|null $preferredLocale
     *
     * @return string
     */
    public function trans(string $key, array $placeholders = [], ?string $preferredLocale = null): string
    {
        $locale = $preferredLocale !== null && $this->isLocaleSupported($preferredLocale)
            ? $preferredLocale
            : $this->locale;

        return $this->innerTranslator->trans($key, $placeholders, null, $locale);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    private function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, $this->supportedLocales, true);
    }
}