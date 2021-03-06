<?php

declare(strict_types=1);

namespace Domynation\Http;

use Exception;

/**
 * Class RouteNotFoundException
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class RouteNotFoundException extends Exception
{
    /**
     * RouteNotFoundException constructor.
     *
     * @param string $routeName
     */
    public function __construct($routeName)
    {
        parent::__construct("Route {$routeName} not found.");
    }
}