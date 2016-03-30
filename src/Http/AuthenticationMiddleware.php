<?php

namespace Domynation\Http;

use Domynation\Authentication\AuthenticatorInterface;
use Domynation\Exceptions\AuthenticationException;

final class AuthenticationMiddleware extends RouterMiddleware
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
        if (!$this->auth->isAuthenticated() && $route->isSecure()) {
            throw new AuthenticationException;
        }

        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}