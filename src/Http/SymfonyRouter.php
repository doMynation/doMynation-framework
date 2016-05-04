<?php

namespace Domynation\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

final class SymfonyRouter
{
    private $router;
    private $routes;
    private $context;
    private $urlMatcher;

    public function __construct(Request $request)
    {
        $this->context = (new RequestContext)->fromRequest($request);
        $this->routes = new RouteCollection;

        $this->routes->add('serviceCase.view', new Route('/serviceCase'));
        $this->routes->add('serviceCase.view', new Route('/user/{id}'));

        $this->urlMatcher = new UrlMatcher($this->routes, $this->context);
    }

    public function match($regex)
    {
        return $this->urlMatcher->match($regex);
    }
}