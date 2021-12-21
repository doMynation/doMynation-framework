<?php

declare(strict_types=1);

namespace Domynation\Http;

/**
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class Action
{
    use BaseActionTrait;

    /**
     * Shortcut to `Translator::trans()`
     *
     * @param string $key
     * @param array $placeholders
     * @param string|null $preferredLocale
     *
     * @return string
     */
    protected function trans(string $key, array $placeholders = [], ?string $preferredLocale = null): string
    {
        return $this->translator->trans($key, $placeholders, $preferredLocale);
    }
}
