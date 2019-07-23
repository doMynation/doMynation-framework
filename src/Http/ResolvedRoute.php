<?php

namespace Domynation\Http;

/**
 * Class ResolvedRoute
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ResolvedRoute
{

    /**
     * @var \Domynation\Http\Route
     */
    private $route;

    /**
     * @var array
     */
    private $parameters;

    /**
     * ResolvedRoute constructor.
     *
     * @param \Domynation\Http\Route $route
     * @param array $parameters
     */
    public function __construct(Route $route, $parameters = [])
    {
        $this->route = $route;
        $this->parameters = $parameters;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}