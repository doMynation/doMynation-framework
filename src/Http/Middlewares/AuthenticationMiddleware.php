<?php

namespace Domynation\Http\Middlewares;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthenticationException;
use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

final class AuthenticationMiddleware extends RouteMiddleware
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
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        if ($resolvedRoute->getRoute()->isSecure() && !$this->user->isAuthenticated()) {
            throw new AuthenticationException;
        }

        return !is_null($this->next) ? $this->next->handle($resolvedRoute, $request) : null;
    }
}