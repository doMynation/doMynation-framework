<?php

namespace Domynation\Http;

use Domynation\Exceptions\AuthorizationException;
use Domynation\Authentication\AuthenticatorInterface;

final class AuthorizationMiddleware extends RouterMiddleware
{

    /**
     * @var \Domynation\Authentication\AuthenticatorInterface
     */
    private $auth;

    public function __construct(AuthenticatorInterface $auth)
    {
        $this->auth = $auth;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        $permissions = $route->getRequiredPermissions();

        if (!empty($permissions)) {
            $user = $this->auth->getUser();

            if ($user->hasPermission($permissions)) {
                throw new AuthorizationException;
            }
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}