<?php

namespace Domynation\Http\Middlewares;

use DI\InvokerInterface;
use Domynation\Http\ResolvedRoute;
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
     * @var \Interop\Container\ContainerInterface
     */
    private $invoker;

    public function __construct(InvokerInterface $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        // Call the route controller using the URI parameters AND inject
        // any dynamically defined dependencies through the container.
        return $this->invoker->call($route->getHandler(), $resolvedRoute->getParameters());
    }
}