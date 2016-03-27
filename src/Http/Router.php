<?php

namespace Domynation\Http;

use Domynation\Bus\CommandBusInterface;
use Domynation\View\ViewFactoryInterface;
use Interop\Container\ContainerInterface;

final class Router
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    /**
     * @var RouterMiddleware
     */
    private $middleware;

    /**
     * The collection of available routes
     *
     * @var Route[]
     */
    private $routes;

    /**
     * @var \Domynation\Bus\CommandBusInterface
     */
    private $bus;

    /**
     * @var ViewFactoryInterface
     */
    private $view;

    public function __construct(ContainerInterface $container, RouterMiddleware ...$middlewares)
    {
        $this->container  = $container;
        $this->middleware = $this->buildMiddlewareChain($middlewares);

        $this->routes = [];
        $this->bus    = $container->get(CommandBusInterface::class);
        $this->view   = $container->get(ViewFactoryInterface::class);
    }

    /**
     * Chains all the provided middlewares together.
     *
     * @param RouterMiddleware[] $middlewares
     *
     * @return RouterMiddleware
     */
    private function buildMiddlewareChain(array $middlewares)
    {
        if (empty($middlewares)) {
            return null;
        }

        // Fetch the next middleware
        $middleware = array_shift($middlewares);

        return $middleware->setNext($this->buildMiddlewareChain($middlewares));
    }

    /**
     * Loads one or more route file.
     *
     * @param string|array $file
     */
    public function load($file)
    {
        // Always work with an array
        $files = is_array($file) ? $file : [$file];

        // Small trick to allow the included file
        // to use $route without the "global" keyword
        $closure = function ($route, $file) {
            require $file;
        };

        foreach ($files as $file) {
            $closure($this, $file);
        }
    }

    /**
     * Registers a route.
     *
     * @param  string $name
     * @param  \Closure $handler
     *
     * @return Route
     */
    public function add($name, \Closure $handler)
    {
        // Bind the handler to the Router class
        $route = new Route($name, $handler->bindTo($this, $this));

        // Insert the route
        $this->routes[$name] = $route;

        return $route;
    }

    /**
     * Handles a request.
     *
     * @param string $routeName The name of the route
     * @param array $inputs The request inputs
     *
     * @return array|mixed
     *
     * @throws \Domynation\Http\RouteNotFoundException
     */
    public function handle($routeName, $inputs)
    {
        if (!array_key_exists($routeName, $this->routes)) {
            throw new RouteNotFoundException($routeName);
        }

        // Fetch the route definition
        $route = $this->routes[$routeName];

        // Handle the request
        $response = $this->middleware->handle($route, $inputs);

        // Make sure the response is an array
        return is_array($response) ? $response : [];
    }

    /**
     * @return RouterMiddleware
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }
}