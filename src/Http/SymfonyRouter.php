<?php

declare(strict_types=1);

namespace Domynation\Http;

use DomainException;
use Domynation\Exceptions\AuthenticationException;
use Domynation\Exceptions\AuthorizationException;
use Domynation\Exceptions\EntityNotFoundException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\Middlewares\RouteMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route as SymfonyRoute;
use Symfony\Component\Routing\RouteCollection;

/**
 * @package Domynation\Http
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class SymfonyRouter implements RouterInterface
{
    private RouteCollection $routes;
    private ?RouteMiddleware $middleware;
    private string $loginRoute;

    /**
     * @param array \Domynation\Http\Middlewares\RouteMiddleware[] $middlewares
     */
    public function __construct(array $middlewares, string $loginRoute)
    {
        $this->middleware = $this->buildMiddlewareChain($middlewares);
        $this->routes = new RouteCollection;
        $this->loginRoute = $loginRoute;
    }

    /**
     * Chains all the provided middlewares together.
     *
     * @param RouteMiddleware[] $middlewares
     *
     * @return RouteMiddleware
     */
    private function buildMiddlewareChain(array $middlewares): ?RouteMiddleware
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
    public function addRoute($method, $path, $controller, $name = null): Route
    {
        // Default the name to the path + method to avoid duplicate
        $name = $name ?: "$path-$method";

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
    public function handle(Request $request): Response
    {
        try {
            return $this->doHandle($request);
        } catch (AuthenticationException $e) {
            return $request->isXmlHttpRequest()
                ? new Response(null, Response::HTTP_UNAUTHORIZED)
                : new RedirectResponse($this->loginRoute);
        } catch (AuthorizationException $e) {
            return $request->isXmlHttpRequest()
                ? new Response(null, Response::HTTP_FORBIDDEN)
                : new RedirectResponse('/403');
        } catch (RouteNotFoundException | EntityNotFoundException $e) {
            return $request->isXmlHttpRequest()
                ? new Response(null, Response::HTTP_NOT_FOUND)
                : new RedirectResponse('/404');
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->getErrors()], Response::HTTP_BAD_REQUEST);
        } catch (DomainException $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function doHandle(Request $request)
    {
        // Resolve the route definition
        try {
            $context = (new RequestContext)->fromRequest($request);
            $resolvedRoute = $this->resolve($context);
        } catch (ResourceNotFoundException $e) {
            throw new RouteNotFoundException($request->getPathInfo());
        }

        // Add the routing uri (e.g. "/customers/{id}") to the request object
        $request->attributes = new ParameterBag($resolvedRoute->getParameters());

        // Let the middlewares do their job
        return $this->middleware->handle($resolvedRoute, $request);
    }

    /**
     * Resolves a route for the provided request.
     *
     * @param \Symfony\Component\Routing\RequestContext $context
     *
     * @return \Domynation\Http\ResolvedRoute
     */
    private function resolve(RequestContext $context)
    {
        // Attempt to match the request to a route
        $matcher = new UrlMatcher($this->routes, $context);
        $matchInfo = $matcher->match($context->getPathInfo());

        // Extract the URI parameters (e.g. {id}) to be to be injected in the controller
        $parameters = array_filter($matchInfo, function ($key) {
            return stripos($key, '_') === false;
        }, ARRAY_FILTER_USE_KEY);

        return new ResolvedRoute($matchInfo['_internalRoute'], $parameters);
    }

    /**
     * @return RouteMiddleware
     */
    public function getMiddleware(): ?RouteMiddleware
    {
        return $this->middleware;
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, $controller, $name = null): Route
    {
        return $this->addRoute('GET', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $controller, $name = null): Route
    {
        return $this->addRoute('POST', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $controller, $name = null): Route
    {
        return $this->addRoute('PUT', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $controller, $name = null): Route
    {
        return $this->addRoute('DELETE', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($path, $controller, $name = null): Route
    {
        return $this->addRoute('PATCH', $path, $controller, $name);
    }

    /**
     * Forwards a request to a different route.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $route
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Domynation\Http\RouteNotFoundException
     */
    public function forward(Request $request, $route): Response
    {
        $context = (new RequestContext)->fromRequest($request);
        $context->setPathInfo($route);

        // Resolve the route definition
        try {
            $resolvedRoute = $this->resolve($context);
        } catch (ResourceNotFoundException $e) {
            throw new RouteNotFoundException($request->getPathInfo());
        }

        // Add the routing uri (e.g. "/customers/{id}") to the request object
        $request->attributes = new ParameterBag($resolvedRoute->getParameters());

        // Let the middlewares do their job
        return $this->middleware->handle($resolvedRoute, $request);
    }
}