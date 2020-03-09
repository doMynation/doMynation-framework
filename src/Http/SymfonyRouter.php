<?php

namespace Domynation\Http;

use Domynation\Exceptions\AuthenticationException;
use Domynation\Exceptions\AuthorizationException;
use Domynation\Exceptions\EntityNotFoundException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\Middlewares\RouteMiddleware;
use PHPUnit\Util\Json;
use Sushi\Common\Exceptions\DomainException;
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

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    /**
     * @var \Domynation\Http\Middlewares\RouteMiddleware
     */
    private $middleware;

    /**
     * @param RouteMiddleware[] $middlewares
     */
    public function __construct($middlewares)
    {
        $this->middleware = $this->buildMiddlewareChain($middlewares);
        $this->routes = new RouteCollection;
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
    public function addRoute($method, $path, $controller, $name = null)
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
        $r = $this->routes->get($name);

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
                : new RedirectResponse('/login');
        } catch (AuthorizationException $e) {
            return $request->isXmlHttpRequest()
                ? new Response(null, Response::HTTP_UNAUTHORIZED)
                : new RedirectResponse('/403');
        } catch (RouteNotFoundException $e) {
            return $request->isXmlHttpRequest()
                ? new Response(null, Response::HTTP_UNAUTHORIZED)
                : new RedirectResponse('/404');
        } catch (ValidationException $e) {
            return new JsonResponse(['errors' => $e->getErrors()], Response::HTTP_BAD_REQUEST);
        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['errors' => [$e->getMessage()]], Response::HTTP_BAD_REQUEST);
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
        $response = $this->middleware->handle($resolvedRoute, $request);

        // Parse the response
        return $this->parseResponse($request, $response);
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
     * Parses the response using a series of helper functions to facilitate the
     * generation of a response from within the controller.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param mixed $response
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function parseResponse(Request $request, $response)
    {
        if ($response === null) {
            return $request->isXmlHttpRequest()
                ? new JsonResponse
                : new Response;
        }

        // Convert a redirect to a regular response for ajax requests
        if ($response instanceof RedirectResponse && $request->isXmlHttpRequest()) {
            $newResponse = new Response('', $response->getStatusCode());
            $newResponse->headers->set('Location', $response->getTargetUrl());

            return $newResponse;
        }

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
    public function get($path, $controller, $name = null)
    {
        return $this->addRoute('GET', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function post($path, $controller, $name = null)
    {
        return $this->addRoute('POST', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function put($path, $controller, $name = null)
    {
        return $this->addRoute('PUT', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path, $controller, $name = null)
    {
        return $this->addRoute('DELETE', $path, $controller, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function patch($path, $controller, $name = null)
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
    public function forward(Request $request, $route)
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
        $response = $this->middleware->handle($resolvedRoute, $request);

        // Parse the response
        return $this->parseResponse($request, $response);
    }
}