<?php

namespace Domynation\Http\Middlewares;

use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

abstract class RouteMiddleware
{

    /**
     * The next middleware in the chain.
     *
     * @var RouteMiddleware
     */
    protected $next;

    /**
     * Handles a route.
     *
     * @param \Domynation\Http\ResolvedRoute $resolvedRoute
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    abstract public function handle(ResolvedRoute $resolvedRoute, Request $request);

    /**
     * @return RouteMiddleware
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Sets the next middleware to handle the request.
     *
     * @param \Domynation\Http\Middlewares\RouteMiddleware $next
     *
     * @return $this
     */
    public function setNext(RouteMiddleware $next = null)
    {
        $this->next = $next;

        return $this;
    }
}