<?php

declare(strict_types=1);

namespace Domynation\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface as SymfonySessionInterface;

/**
 * Interface SessionInterface
 */
interface SessionInterface extends SymfonySessionInterface
{
    /**
     * Saves and closes the session.
     * Alias to `save()`.
     */
    public function close(): void;
}