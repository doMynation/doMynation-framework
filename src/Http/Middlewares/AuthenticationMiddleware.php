<?php

namespace Domynation\Http\Middlewares;

use Domynation\Authentication\UserInterface;
use Domynation\Exceptions\AuthenticationException;
use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuthenticationMiddleware
 *
 * @package Domynation\Http\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class AuthenticationMiddleware extends RouteMiddleware
{

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * AuthenticationMiddleware constructor.
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
        if ($resolvedRoute->getRoute()->isSecure() && !$this->user->isAuthenticated()) {
            throw new AuthenticationException;
        }

        return $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;
    }
}