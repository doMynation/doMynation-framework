<?php

namespace Domynation\Http;

use Domynation\Exceptions\AuthenticationException;

final class AuthenticationMiddleware extends RouterMiddleware
{

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if (!\User::isLogged() && $route->isSecure()) {
            throw new AuthenticationException;
        }

        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}