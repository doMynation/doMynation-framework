<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthorizationException;
use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthorizationMiddleware
 *
 * @package Domynation\Http\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class AuthorizationMiddleware extends RouteMiddleware
{
    /**
     * @var UserInterface
     */
    private UserInterface $user;

    /**
     * AuthorizationMiddleware constructor.
     *
     * @param \Domynation\Authentication\UserInterface $user
     */
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

        return $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;
    }
}