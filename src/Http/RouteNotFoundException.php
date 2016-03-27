<?php

namespace Domynation\Http;

final class RouteNotFoundException extends \Exception
{
    public function __construct($routeName)
    {
        parent::__construct("Route {$routeName} not found.");
    }
}