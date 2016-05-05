<?php

namespace Domynation\Http;


use Symfony\Component\HttpFoundation\Request;

interface RouterInterface
{

    /**
     * Registers a GET route.
     *
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function get($path, callable $controller, $name = null);

    /**
     * Registers a POST route.
     *
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function post($path, callable $controller, $name = null);

    /**
     * Registers a PATCH route.
     *
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function patch($path, callable $controller, $name = null);

    /**
     * Registers a DELETE route.
     *
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function delete($path, callable $controller, $name = null);

    /**
     * Registers a PUT route.
     *
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function put($path, callable $controller, $name = null);

    /**
     * Registers a route.
     *
     * @param string $method
     * @param string $path
     * @param callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function addRoute($method, $path, callable $controller, $name = null);

    /**
     * Handles a route.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Domynation\Http\RouteNotFoundException
     */
    public function handle(Request $request);

    /**
     * @return \Domynation\Http\Middlewares\RouteMiddleware
     */
    public function getMiddleware();
}