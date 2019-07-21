<?php

namespace Domynation\Http\Middlewares;

use Assert\AssertionFailedException;
use Domynation\Exceptions\ValidationException;
use Domynation\Http\ResolvedRoute;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * A middleware responsible for calling the appropriate validator
 * for a request.
 *
 * @see Domynation\Validation\RouteValidator
 * @package Domynation\Http\Middlewares
 * @author Dominique Sarrazin <domynation@gmail.com>
 */
final class ValidationMiddleware extends RouteMiddleware
{

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * ValidationMiddleware constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
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
                if (method_exists($validator, 'validateRequest')) {
                    // If a `validateRequest` method exists on the validator, call it.
                    $validator->validateRequest($request);
                } else {
                    // Fall back to the traditional `validate` method.
                    $validator->validate($request->request->all());
                }
            } catch (AssertionFailedException $e) {
                throw new ValidationException([$e->getMessage()]);
            }
        }

        // Pass the request to the next middleware
        return $this->next !== null ? $this->next->handle($resolvedRoute, $request) : null;
    }
}