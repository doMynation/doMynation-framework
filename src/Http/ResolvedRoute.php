<?php

namespace Domynation\Http;

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

    public function __construct(Route $route, $parameters = [])
    {
        $this->route      = $route;
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