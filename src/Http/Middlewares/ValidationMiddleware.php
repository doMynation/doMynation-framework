<?php

namespace Domynation\Http\Middlewares;

use Assert\AssertionFailedException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\ResolvedRoute;
use Interop\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

final class ValidationMiddleware extends RouteMiddleware
{

    /**
     * @var \Interop\Container\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ResolvedRoute $resolvedRoute, Request $request)
    {
        $route = $resolvedRoute->getRoute();

        if ($route->hasValidator()) {
            // Instanciate the validator class
            $validator = $this->container->get($route->getValidator());

            try {
                $validator->validate($request->request->all());
            } catch (AssertionFailedException $e) {
                throw new ValidationException([$e->getMessage()]);
            }
        }

        // Pass the request to the next middleware
        return !is_null($this->next) ? $this->next->handle($resolvedRoute, $request) : null;
    }
}