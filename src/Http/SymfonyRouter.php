<?php

namespace Domynation\Http;

use Domynation\Http\Middlewares\RouteMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

final class SymfonyRouter implements RouterInterface
{

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    /**
     * @var \Domynation\Http\Middlewares\RouteMiddleware
     */
    private $middleware;

    /**
     * SymfonyRouter constructor.
     *
     * @param RouteMiddleware[] $middlewares
     */
    public function __construct($middlewares)
    {
        $this->middleware = $this->buildMiddlewareChain($middlewares);
        $this->routes      = new RouteCollection;
    }

    /**
     * Chains all the provided middlewares together.
     *
     * @param RouteMiddleware[] $middlewares
     *
     * @return RouteMiddleware
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
     * {@inheritdoc}
     */
    public function addRoute($method, $path, callable $controller, $name = null)
    {
        // Default the name to the path
        $name = $name ?: $path;

        $domynationRoute = new Route($name, $controller, $method);

        $route = new SymfonyRoute($path);
        $route->setMethods($method);
        $route->setDefaults([
            '_internalRoute' => $domynationRoute
        ]);

        // Add the route to the collection
        $this->routes->add($name, $route);

        // Return the route
        return $domynationRoute;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request)
    {
        // Resolve the route definition
        try {
            $resolvedRoute = $this->resolve($request);
        } catch (ResourceNotFoundException $e) {
            throw new RouteNotFoundException($request->getPathInfo());
        }

        // Let the middlewares do their jobs
        $response = $this->middleware->handle($resolvedRoute, $request);

        // Parse the response
        return $this->parseResponse($response);
    }

    /**
     * Resolves a route for the provided request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return ResolvedRoute
     */
    private function resolve(Request $request)
    {
        // Attempt to match the request to a route
        $matcher   = new UrlMatcher($this->routes, (new RequestContext)->fromRequest($request));
        $matchInfo = $matcher->match($request->getPathInfo());

        // Extract the URI parameters (e.g. {id}) to be to be injected in the controller
        $parameters = array_filter($matchInfo, function ($key) {
            return stripos($key, '_') === false;
        }, ARRAY_FILTER_USE_KEY);

        return new ResolvedRoute($matchInfo['_internalRoute'], $parameters);
    }

    /**
     * Parses the response using a series of helper functions to facilitate the
     * generation of a response from within the controller.
     *
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function parseResponse($response)
    {
        if ($response instanceof Response) {
            return $response;
        }

        if (is_string($response)) {
            return new Response($response);
        }

        if (is_array($response)) {
            return new JsonResponse($response);
        }
    }

    /**
     * @return RouteMiddleware
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, callable $controller, $name = null)
    {
        return $this->addRoute('GET', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, callable $controller, $name = null)
    {
        return $this->addRoute('POST', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, callable $controller, $name = null)
    {
        return $this->addRoute('PUT', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, callable $controller, $name = null)
    {
        return $this->addRoute('DELETE', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($path, callable $controller, $name = null)
    {
        return $this->addRoute('PATCH', $path, $controller, $name);
    }
}