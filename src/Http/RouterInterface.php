<?php

declare(strict_types=1);

namespace Domynation\Http;

use Domynation\Http\Middlewares\RouteMiddleware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface RouterInterface
 *
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
interface RouterInterface
{

    /**
     * Registers a GET route.
     *
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return \Domynation\Http\Route
     */
    public function get($path, $controller, $name = null): Route;

    /**
     * Registers a POST route.
     *
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return \Domynation\Http\Route
     */
    public function post($path, $controller, $name = null): Route;

    /**
     * Registers a PATCH route.
     *
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return \Domynation\Http\Route
     */
    public function patch($path, $controller, $name = null): Route;

    /**
     * Registers a DELETE route.
     *
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return \Domynation\Http\Route
     */
    public function delete($path, $controller, $name = null): Route;

    /**
     * Registers a PUT route.
     *
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return Route
     */
    public function put($path, $controller, $name = null): Route;

    /**
     * Registers a route.
     *
     * @param string $method
     * @param string $path
     * @param string|callable $controller
     * @param string|null $name
     *
     * @return \Domynation\Http\Route
     */
    public function addRoute($method, $path, $controller, $name = null): Route;

    /**
     * Handles a route.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Domynation\Http\RouteNotFoundException
     */
    public function handle(Request $request): Response;

    /**
     * @return \Domynation\Http\Middlewares\RouteMiddleware
     */
    public function getMiddleware(): ?RouteMiddleware;

    /**
     * Forwards a request to a different route.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $route
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forward(Request $request, $route): Response;
}