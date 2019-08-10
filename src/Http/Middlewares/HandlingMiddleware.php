<?php

namespace Domynation\Http\Middlewares;

use Domynation\Http\ResolvedRoute;
use Domynation\Session\SessionInterface;
use Invoker\InvokerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class HandlingMiddleware
 *
 * This middleware has to be the last in the chain since it returns the final response.
 *
 * @package Domynation\Http\Middleware
 */
final class HandlingMiddleware extends RouteMiddleware
{

    /**
     * @var \Invoker\InvokerInterface
     */
    private $invoker;
    /**
     * @var \Domynation\Session\SessionInterface
     */
    private $session;

    /**
     * HandlingMiddleware constructor.
     *
     * @param \Invoker\InvokerInterface $invoker
     * @param \Domynation\Session\SessionInterface $session
     */
    public function __construct(InvokerInterface $invoker, SessionInterface $session)
    {
        $this->invoker = $invoker;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        // Close the session for writes for routes that aren't going
        // to write anything to the session.
        if ($resolvedRoute->getRoute()->isReadOnly()) {
            $this->session->close();
        }

        // Call the route controller using the URI parameters AND inject
        // any dynamically defined dependencies through the container.
        return $this->invoker->call($route->getHandler(), $resolvedRoute->getParameters());
    }
}