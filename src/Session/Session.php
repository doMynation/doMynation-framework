<?php

declare(strict_types=1);

namespace Domynation\Session;

use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

/**
 * A decorator for Symfony's session to avoid coupling userland code with Symfony directly.
 *
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class Session extends SymfonySession implements SessionInterface
{
    /**
     * Alias for `save()`.
     */
    public function close(): void
    {
        $this->save();
    }
}
