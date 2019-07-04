<?php

namespace Domynation\Http;

/**
 * Class RouterMiddleware
 *
 * @package Domynation\Http
 * @deprecated Use RouteMiddleware instead
 */
abstract class RouterMiddleware
{
    /**
     * The next middleware in the chain.
     *
     * @var RouterMiddleware
     */
    protected $next;

    /**
     * Handles provided route.
     *
     * @param \Domynation\Http\Route $route
     * @param array $inputs
     *
     * @return mixed
     */
    abstract public function handle(Route $route, array $inputs);

    /**
     * @return RouterMiddleware
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * @param RouterMiddleware $next
     *
     * @return $this
     */
    public function setNext(RouterMiddleware $next = null)
    {
        $this->next = $next;

        return $this;
    }
}