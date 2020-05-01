<?php

declare(strict_types=1);

namespace Domynation\Http;

/**
 * Class ResolvedRoute
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ResolvedRoute
{
    private Route $route;
    private array $parameters;

    public function __construct(Route $route, array $parameters = [])
    {
        $this->route = $route;
        $this->parameters = $parameters;
    }

    public function getRoute(): Route
    {
        return $this->route;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}