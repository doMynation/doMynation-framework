<?php

declare(strict_types=1);

namespace Domynation\Http\Middlewares;

use Domynation\Http\ResolvedRoute;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RouteMiddleware
 *
 * @package Domynation\Http\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
abstract class RouteMiddleware
{

    /**
     * The next middleware in the chain.
     *
     * @var RouteMiddleware
     */
    protected ?RouteMiddleware $next;

    /**
     * Handles a route.
     *
     * @param \Domynation\Http\ResolvedRoute $resolvedRoute
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return mixed
     */
    abstract public function handle(ResolvedRoute $resolvedRoute, Request $request);

    public function getNext(): ?RouteMiddleware
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