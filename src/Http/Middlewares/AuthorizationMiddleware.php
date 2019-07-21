<?php

namespace Domynation\Http\Middlewares;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthorizationException;
use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

final class AuthorizationMiddleware extends RouteMiddleware
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
        $requiredPermissions = $resolvedRoute->getRoute()->getRequiredPermissions();

        if (!empty($requiredPermissions) && !$this->user->hasPermission($requiredPermissions)) {
            throw new AuthorizationException;
        }

        return !is_null($this->next) ? $this->next->handle($resolvedRoute, $request) : null;
    }
}