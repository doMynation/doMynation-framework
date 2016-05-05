<?php

namespace Domynation\Http;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthenticationException;

final class AuthenticationMiddleware extends RouterMiddleware
{

    /**
     * @var UserInterface
     */
    private $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Route $route, array $inputs)
    {
        if ($route->isSecure() && !$this->user->isAuthenticated()) {
            throw new AuthenticationException;
        }

        return !is_null($this->next) ? $this->next->handle($route, $inputs) : null;
    }
}