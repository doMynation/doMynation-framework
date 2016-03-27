<?php

namespace Domynation\Http;

use Domynation\Exceptions\AuthorizationException;

final class AuthorizationMiddleware extends RouterMiddleware
{

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        $permissions = $route->getRequiredPermissions();

        if (!\User::hasAccess($permissions)) {
            throw new AuthorizationException;
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}